<?php

namespace OnzaMe\Helpers\Http\Controllers;

use OnzaMe\Helpers\RequestFiltersHandler;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    public function getCollectionResponse($request, string $collectionResourceClass, Builder $query, RequestFiltersHandler $filterHandler = null)
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
