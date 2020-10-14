<?php


namespace OnzaMe\Helpers\Services;

use OnzaMe\Helpers\Models\ExternalRequest;
use Illuminate\Database\Eloquent\Builder;

class ExternalRequestService
{
    public function getByUrl(string $url)
    {
        return $this->externalRequestQuery($url)->first();
    }

    public function existsByUrl(string $url): bool
    {
        return $this->externalRequestQuery($url)->exists();
    }

    public function save(string $url, array $reponse, bool $force = false)
    {
        if ($this->existsByUrl($url) && !$force) {
            return;
        }
        ExternalRequest::query()->create([
            'response' => $reponse,
            'url' => $url
        ]);
    }

    public function firstOrCreate(string $url, array $reponse, bool $force = false): ExternalRequest
    {
        /** @var ExternalRequest $externalRequest */
        $externalRequest = null;
        if ($this->existsByUrl($url) && !$force) {
            $externalRequest = $this->externalRequestQuery($url)->first();
            return $externalRequest;
        }

        $externalRequest = ExternalRequest::query()->create([
            'response' => $reponse,
            'url' => $url
        ]);

        return $externalRequest;
    }

    protected function externalRequestQuery(string $url): Builder
    {
        return ExternalRequest::query()->where('url', $url);
    }
}
