<?php

namespace OnzaMe\Helpers\Traits;

use Illuminate\Database\Eloquent\Builder;
use OnzaMe\Helpers\Contracts\RequestFiltersContract;
use OnzaMe\Helpers\RequestFiltersHandler;

trait CollectionResponse
{
    /**
     * @param $request
     * @param string $collectionResourceClass
     * @param Builder $query
     * @param RequestFiltersContract|null $filterHandler
     * @return \Illuminate\Http\JsonResponse|mixed
     * @throws \OnzaMe\Helpers\Exceptions\UnproccessableHttpRequestException
     */
    public function getCollectionResponse($request, string $collectionResourceClass, $query, RequestFiltersContract $filterHandler = null)
    {
        $filterHandler = $filterHandler ?? new RequestFiltersHandler($request);

        if ($request->exists('only_count')) {
            return response()->json([
                'data' => $filterHandler->apply($query)->count()
            ]);
        }

        return new $collectionResourceClass($filterHandler->applyAndPaginate($query));
    }
}
