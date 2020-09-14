<?php

namespace OnzaMe\Helpers\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Http\Resources\Json\ResourceCollection;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class BaseCollection extends ResourceCollection
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        /** @var LengthAwarePaginator $paginate */
        $paginate = $this;

        if (empty($this->collection)) {
            return parent::toArray($request);
        }

        try {
            return [
                "total" => $paginate->total(),
                "per_page" => $paginate->perPage(),
                "first_page_url" => $paginate->url(1),
                "last_page_url" => $paginate->url($paginate->lastPage()),
                "next_page_url" => $paginate->nextPageUrl(),
                "prev_page_url" => $paginate->previousPageUrl(),
                "path" => $paginate->getOptions()['path'],
                "data" => $this->collection->map(function ($item) use ($request) {
                    if (!$this->collects()) {
                        if (is_a($item, JsonResource::class) || is_a($item, ResourceCollection::class)) {
                            return $item->toArray($request);
                        }
                        return $item->toArray();
                    }
                    $collectsClass = $this->collects();
                    return new $collectsClass($item);
                })
            ];
        } catch (\Exception $ex) {
            return parent::toArray($request);
        }
    }

    public function withResponse($request, $response)
    {
        $originalContent = $response->getData(true);
        unset($originalContent['links'],$originalContent['meta']);
        $response->setData($originalContent);
    }
}
