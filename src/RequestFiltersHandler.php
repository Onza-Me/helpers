<?php

namespace OnzaMe\Helpers;

use OnzaMe\Helpers\Exceptions\UnproccessableHttpRequestException;
use OnzaMe\Helpers\Services\Contracts\RequestFiltersContract;
use OnzaMe\Helpers\Services\Dadata\DadataService;
use OnzaMe\Helpers\Services\Dadata\Location\AreaDadataService;
use OnzaMe\Helpers\Services\Dadata\Location\CityDadataService;
use OnzaMe\Helpers\Services\Dadata\Location\CityDistrictDadataService;
use OnzaMe\Helpers\Services\Dadata\Location\HouseDadataService;
use OnzaMe\Helpers\Services\Dadata\Location\RegionDadataService;
use OnzaMe\Helpers\Services\Dadata\Location\SettlementDadataService;
use OnzaMe\Helpers\Services\Dadata\Location\StreetDadataService;
use OnzaMe\Helpers\Services\FormScheme\OfferFormSchemeService;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

class RequestFiltersHandler implements RequestFiltersContract
{
    private Request $request;
    private array $filters = [];

    public function __construct(Request $request)
    {
        $this->request = $request;
        $this->extractFilters($request);
    }

    private function extractFilters(Request $request)
    {
        $filters = $request->get('filter') ?? [];

        foreach ($filters as $key => $filter) {
            if (is_numeric($key)) {
                $this->addFilter(
                    $filter['key'],
                    $filter['value'],
                    $filter['operator'] ?? '='
                );
            } else {
                $this->addFilter(
                    $key,
                    $filter['value'],
                    $filter['operator'] ?? '='
                );
            }
        }
    }

    public function getAddressFilters(string $address = null): array
    {
        if (empty(trim($address))) {
            return [];
        }
        $data = [];
        $dadata = app(DadataService::class);
        $addressInfo = null;
        try {
            $addressInfo = $dadata->getAddressInfo($address);
        } catch (\Exception $e) {
            return [];
        }

        $data['house_number'] = $addressInfo->getHouseNumber(null);
        $data['region_id'] = app(RegionDadataService::class)->createOrFind($addressInfo)->id;
        $data['city_id'] = app(CityDadataService::class)->createOrFind($addressInfo)->id;
        $data['city_district_id'] = app(CityDistrictDadataService::class)->createOrFind($addressInfo)->id;
        $data['area_id'] = app(AreaDadataService::class)->createOrFind($addressInfo)->id;
        $data['settlement_id'] = app(SettlementDadataService::class)->createOrFind($addressInfo)->id;
        $data['house_id'] = app(HouseDadataService::class)->createOrFind($addressInfo)->id;
        $data['street_id'] = app(StreetDadataService::class)->createOrFind($addressInfo)->id;

        return array_filter($data, function ($addressItem) {
            return !empty($addressItem);
        });
    }

    public function addFilter($key, $value = null, string $operator = '=', string $method = 'where')
    {
        if ($key === 'address') {
            $addressFilters = $this->getAddressFilters($value);
            foreach ($addressFilters as $key => $value) {
                $this->addFilter($key, $value);
            }
            return $this;
        }
        if ($method === 'where' && is_a($key, \Closure::class)) {
            $this->filters[] = $key;
        }
        $this->filters[] = [
            'key' => $key,
            'value' => $value,
            'operator' => $operator,
            'method' => $method
        ];

        return $this;
    }

    /**
     * @param Builder $builder
     * @return Builder
     */
    public function apply($builder)
    {
        $offerSchemeService = app(OfferFormSchemeService::class);
        foreach ($this->filters as $filter) {
            if (is_array($filter)) {
                $field = $offerSchemeService->findFieldByKey($filter['key']);
                $existsType = !empty($field) && isset($field['type']);
                $isJson = $existsType && $field['type'] === 'json';
                $isMultipleRadio = $existsType && $field['type'] === 'radio' && isset($field['multiple']) && $field['multiple'];
                if ($isJson || $isMultipleRadio) {
                    /** @var Builder $builder */
                    $builder = $builder->where(function (Builder $builder) use ($filter) {
                        if (is_array($filter['value'])) {
                            $builder->whereJsonContains($filter['key'], $filter['value'][0]);
                            foreach ($filter['value'] as $index => $value) {
                                if ($index === 0) {
                                    continue;
                                }
                                $builder->orWhereJsonContains($filter['key'], $value);
                            }
                        } else {
                            $builder->whereJsonContains($filter['key'], $filter['value']);
                        }
                    });
                    continue;
                }
                if ($filter['method'] === 'whereNotIn') {
                    $builder = $builder->{$filter['method']}($filter['key'], $filter['value']);
                } else if (is_array($filter['value'])) {
                    $builder = $builder->whereIn($filter['key'], $filter['value']);
                } else {
                    $builder = $builder->{$filter['method']}($filter['key'], $filter['operator'], $filter['value']);
                }
                continue;
            }
            $builder = $filter($builder);
        }

        return $builder;
    }

    /**
     * @param Builder $builder
     * @param int $perPage
     * @param string[] $columns
     * @param string $pageName
     * @param int $page
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     * @throws UnproccessableHttpRequestException
     */
    public function applyAndPaginate($builder, $perPage = 10, $columns = ['*'], $pageName = 'page', $page = 1)
    {
        $perPage = $this->request->get('per_page') ?? $perPage;

        if ($perPage < 10 || $perPage > 100) {
            throw new UnproccessableHttpRequestException('Ошибка валидации запроса', 'Кол-во записей не должно быть меньше 10 или больше 100', [
                'per_page' => 'Кол-во записей не должно быть меньше 10 или больше 100'
            ]);
        }

        $page = $this->request->get('page') ?? $page;

        $requestOrderHandler = new RequestOrderHandler($this->request);

        return $requestOrderHandler->apply($this->apply($builder))->paginate(
            $perPage,
            $columns,
            'page',
            $page
        );
    }
}
