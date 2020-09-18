<?php

namespace OnzaMe\Helpers;

use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Str;
use OnzaMe\Helpers\Contracts\RequestFiltersContract;
use OnzaMe\Helpers\Exceptions\UnproccessableHttpRequestException;

class RequestFiltersHandler implements RequestFiltersContract
{
    protected Request $request;
    protected array $filters = [];

    public function __construct(Request $request)
    {
        $this->request = $request;
        $this->extractFilters($request);
    }

    protected function extractFilters(Request $request)
    {
        $filters = get_converted_json_from_request($request, 'filter');

        foreach ($filters as $key => $filter) {
            if (is_numeric($key)) {
                $this->addFilter(
                    $filter['key'],
                    $filter['value'],
                    $filter['operator'] ?? '=',
                    $filter['method'] ?? 'where',
                    null,
                    $filter['is_or'] ?? false,
                );
            } else {
                $this->addFilter(
                    $key,
                    $filter['value'],
                    $filter['operator'] ?? '=',
                    $filter['method'] ?? 'where',
                    null,
                    $filter['is_or'] ?? false,
                );
            }
        }
    }

    /**
     * @param \Closure|string $key
     * @param null $value
     * @param string $operator
     * @param string $method
     * @param string|null $fieldType
     * @param bool $isOr
     * @return $this|mixed
     */
    public function addFilter($key, $value = null, string $operator = '=', string $method = 'where', $fieldType = null, bool $isOr = false)
    {
        if ($method === 'where' && is_a($key, \Closure::class)) {
            $this->filters[] = $key;
            return $this;
        }
        $this->filters[] = [
            'key' => $key,
            'field_type' => $fieldType,
            'value' => $value,
            'operator' => $operator,
            'method' => $method,
            'is_or' => $isOr
        ];

        return $this;
    }

    /**
     * @param Builder $builder
     * @return Builder
     */
    public function apply($builder)
    {

        foreach ($this->filters as $filter) {
            if (is_callable($filter)) {
                $builder = $filter($builder);
                continue;
            }

            if (isset($filter['field_type']) && $filter['field_type'] === 'json') {
                /** @var Builder $builder */
                $builder = $builder->{$this->getConditionMethod('where', $filter['is_or'])}(function (Builder $builder) use ($filter) {
                    if (is_array($filter['value'])) {
                        $builder->{$this->getConditionMethod('whereJsonContains', $filter['is_or'])}($filter['key'], $filter['value'][0]);
                        foreach ($filter['value'] as $index => $value) {
                            if ($index === 0) {
                                continue;
                            }
                            $builder->{$this->getConditionMethod('orWhereJsonContains', $filter['is_or'])}($filter['key'], $value);
                        }
                    } else {
                        $builder->{$this->getConditionMethod('whereJsonContains', $filter['is_or'])}($filter['key'], $filter['value']);
                    }
                });
                continue;
            }
            if ($filter['method'] === 'whereNotIn') {
                $builder = $builder->{$filter['method']}($filter['key'], $filter['value']);
            } else if (is_array($filter['value'])) {
                $builder = $builder->{$this->getConditionMethod('whereIn', $filter['is_or'])}($filter['key'], $filter['value']);
            } else {
                $builder = $builder->{$this->getConditionMethod($filter['method'], $filter['is_or'])}($filter['key'], $filter['operator'], $filter['value']);
            }
        }

        return $builder;
    }

    private function getConditionMethod(string $method, bool $isOr = false): string
    {
        if (!$isOr) {
            return $method;
        } else if (preg_match('/^or/', $method)) {
            return $method;
        }
        return Str::camel('or_'.$method);
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
