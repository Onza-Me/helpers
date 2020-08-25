<?php


namespace OnzaMe\Helpers\Contracts;


use Illuminate\Database\Eloquent\Builder;

interface RequestFiltersContract
{
    /**
     * @param \Closure|string $key
     * @param string|mixed $value
     * @param string $operator
     * @param string $method
     * @return mixed
     */
    public function addFilter($key, $value = null, string $operator = '=', string $method = 'where');

    /**
     * @param Builder $builder
     * @return Builder
     */
    public function apply($builder);
}
