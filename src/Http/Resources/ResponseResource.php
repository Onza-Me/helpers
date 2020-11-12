<?php

namespace OnzaMe\Helpers\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ResponseResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            $this->mergeWhenNotNull('success'),
            $this->mergeWhenNotNull('data'),
            $this->mergeWhenNotNull('error'),
            $this->mergeWhenNotNull('fields')
        ];
    }

    protected function mergeWhenNotNull(string $fieldName)
    {
        return $this->mergeWhen(!is_null($this->{$fieldName}), [$fieldName => $this->{$fieldName}]);
    }
}
