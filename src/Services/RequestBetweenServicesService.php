<?php

namespace OnzaMe\Helpers\Services;

use Illuminate\Http\Client\PendingRequest;
use Illuminate\Support\Facades\Http;
use OnzaMe\JWT\Models\AccessToken;

class RequestBetweenServicesService
{
    protected PendingRequest $request;
    protected string $serviceName;

    public function __construct(string $serviceName)
    {
        $this->request = Http::withHeaders($this->getHeaders());
        $this->serviceName = $serviceName;
    }

    public function getRequest(): PendingRequest
    {
        return $this->request;
    }

    public function getHeaders()
    {
        return [
            'Authorization' => 'Bearer '.(new AccessToken([
                    'user' => [
                        'id' => 0,
                        'role' => 'server'
                    ]
                ]))->token
        ];
    }

    public function get(string $url, $query = null)
    {
        return $this->request->get($this->url($url), $query);
    }

    public function post(string $url, array $data = [])
    {
        return $this->request->post($this->url($url), $data);
    }

    public function put(string $url, array $data = [])
    {
        return $this->request->put($this->url($url), $data);
    }

    public function delete(string $url, array $data = [])
    {
        return $this->request->delete($this->url($url), $data);
    }

    public function url(string $uri, $service = null)
    {
        $serviceName = $service ?? $this->serviceName;

        $urlPrefix = trim(config('onzame_helpers.services.'.$serviceName), '/');
        return $urlPrefix.'/'.trim($uri, '/');
    }
}
