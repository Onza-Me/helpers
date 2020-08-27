<?php

namespace OnzaMe\Helpers;

use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Builder;
use OnzaMe\Helpers\Contracts\RequestOrderContract;
use OnzaMe\Helpers\Exceptions\UnproccessableHttpRequestException;

class RequestOrderHandler implements RequestOrderContract
{
    private Request $request;
    private array $orders = [];

    public function __construct(Request $request)
    {
        $this->request = $request;
        $this->extractOrders($request);
    }

    public function extractOrders(Request $request)
    {
        $orders = get_converted_json_from_request($request, 'order');

        foreach ($orders as $key => $ordering) {
            $this->addOrder($key, $ordering);
        }
    }

    public function addOrder($key, $ordering = 'desc')
    {
        if (isset($this->orders[$key])) {
            throw new UnproccessableHttpRequestException('Ошибка сортировки', 'Вы можете применить только одно правильно для поля при сортировке');
        }
        $this->orders[$key] = $ordering;
    }

    /**
     * @param Builder $builder
     * @return Builder
     */
    public function apply($builder)
    {
        foreach ($this->orders as $key => $ordering) {
            $builder = $builder->orderBy($key, $ordering);
        }

        return $builder;
    }
}
