<?php

namespace OnzaMe\Helpers\Http\Controllers;

use OnzaMe\Helpers\Contracts\RequestFiltersContract;
use OnzaMe\Helpers\RequestFiltersHandler;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

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
