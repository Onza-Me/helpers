<?php

namespace OnzaMe\Helpers;

use DB;
use Cache;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Str;
use OnzaMe\Helpers\Contracts\RequestFiltersContract;
use OnzaMe\Helpers\Exceptions\UnproccessableHttpRequestException;

class RequestFiltersHandler implements RequestFiltersContract
{
    protected Request $request;
    protected array $filters = [];
    protected array $cols = [];

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
                    $filter['field_type'] ?? null,
                    $filter['is_or'] ?? false,
                );
            } else {
                $this->addFilter(
                    $key,
                    $filter['value'],
                    $filter['operator'] ?? '=',
                    $filter['method'] ?? 'where',
                    $filter['field_type'] ?? null,
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
            'value' => is_json($value) && is_array($decodedValue = json_decode($value, true)) ? $decodedValue : $value,
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
        $builder = $builder->where(function (Builder $builder) {
            foreach ($this->filters as $filter) {
                if (is_callable($filter)) {
                    $builder = $filter($builder);
                    continue;
                }

                if ($filter['operator'] === 'ilike' && $filter['value'] === '%%') {
                    continue;
                }

                $filter = $this->prepareRelationInFilterArray($filter);
                if (!empty($filter['relation'])) {
                    $builder = $this->applyFilterWithRelation($builder, $filter);
                } else {
                    $builder = $this->applyFilter($builder, $filter);
                }
            }
        });

        return $builder;
    }

    private function applyFilterWithRelation(Builder $builder, array $filter): Builder
    {
        return $builder->{ $this->getQueryFilterRelationMethodName($filter) }(
            $filter['relation'],
            function (Builder $builder) use ($filter) {
                $this->applyFilter($builder, $filter);
            }
        );
    }

    private function getQueryFilterRelationMethodName(array $filter): string
    {
        return (isset($filter['is_or']) && parse_boolean_var($filter['is_or']) ? 'orW' : 'w').'hereHas';
    }

    protected function prepareRelationInFilterArray(array $filter): array
    {
        $explodedKey = explode('.', $filter['key']);

        $key = $filter['key'];
        $relation = '';

        if (count($explodedKey) > 1) {
            $relation = $explodedKey[0] ?? $relation;
            $key = $explodedKey[1];
        }

        $filter['key'] = $key;
        $filter['relation'] = $relation;

        return $filter;
    }

    protected function getModelColumnsCacheKey(Builder $builder): string
    {
        return 'table.'.$builder->getModel()->getTable().'.columns';
    }

    protected function getModelColumns(Builder $builder)
    {
        if (!empty($this->cols)) {
            return $this->cols;
        }

        $cols = json_decode(\Cache::get($this->getModelColumnsCacheKey($builder), '[]'), true);
        if (!empty($cols)) {
            return $this->cols = $cols;
        }

        return $this->getFreshModelColumns($builder);
    }

    protected function getModelColumnType(Builder $builder, string $columnKey)
    {
        $cols = $this->getModelColumns($builder);
        $col = collect($cols)->where('column_name', $columnKey)->first();
        return $col['udt_name'] ?? null;
    }

    protected function getFreshModelColumns(Builder $builder)
    {
        $result = \DB::select(
            'select column_name, udt_name from information_schema.columns where table_name = \''.$builder->getModel()->getTable().'\';'
        );
        Cache::add($this->getModelColumnsCacheKey($builder), json_encode($result), 604800);
        return $this->cols = $result;
    }

    protected function applyFilter(Builder &$builder, array $filter)
    {
        $filter['field_type'] = !empty($filter['field_type']) ? $filter['field_type'] : $this->getModelColumnType($builder, $filter['key']);
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
                    return $builder->{$this->getConditionMethod('whereJsonContains', $filter['is_or'])}($filter['key'], $filter['value']);
                }
            });
            return $builder;
        }
        if ($filter['operator'] === '!=' && $filter['value'] === null) {
            return $builder->{$this->getConditionMethod('whereNotNull', $filter['is_or'])}($filter['key']);
        }
        if ($filter['operator'] === '=' && $filter['value'] === null) {
            return $builder->{$this->getConditionMethod('whereNull', $filter['is_or'])}($filter['key']);
        }
        if ($filter['method'] === 'whereNotIn') {
            return $builder->{$filter['method']}($filter['key'], $filter['value']);
        } else if (is_array($filter['value'])) {
            return $builder->{$this->getConditionMethod('whereIn', $filter['is_or'])}($filter['key'], $filter['value']);
        } else {
            return $builder->{$this->getConditionMethod($filter['method'], $filter['is_or'])}($filter['key'], $filter['operator'], $filter['value']);
        }
    }

    protected function getConditionMethod(string $method, bool $isOr = false): string
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

        $page = $this->request->get($pageName) ?? $page;

        $requestOrderHandler = new RequestOrderHandler($this->request);

        return $requestOrderHandler->apply($this->apply($builder))->paginate(
            $perPage,
            $columns,
            'page',
            $page
        );
    }
}
