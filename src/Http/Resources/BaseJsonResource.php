<?php

namespace OnzaMe\Helpers\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class BaseJsonResource extends JsonResource
{
    protected function mergeWhenNotNull(string $fieldName)
    {
        return $this->mergeWhen(!is_null($this->{$fieldName}), [$fieldName => $this->{$fieldName}]);
    }
}
