<?php


namespace OnzaMe\Helpers\Services\Contracts;

use Illuminate\Http\Client\PendingRequest;

interface RequestBetweenServicesServiceContract
{
    public function __construct(string $serviceName = 'main');

    public function getRequest(): PendingRequest;

    public function getHeaders();

    public function get(string $url, $query = null);

    public function post(string $url, array $data = []);

    public function put(string $url, array $data = []);

    public function delete(string $url, array $data = []);

    public function url(string $uri, $service = null);
}
