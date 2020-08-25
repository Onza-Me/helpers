<?php

namespace OnzaMe\Helpers\Contracts;

use Illuminate\Database\Eloquent\Builder;

interface RequestOrderContract
{
    /**
     * @param \Closure|string $key
     * @param string|mixed $ordering
     * @return mixed
     */
    public function addOrder($key, $ordering = 'desc');

    /**
     * @param Builder $builder
     * @return Builder
     */
    public function apply($builder);
}
