<?php

namespace OnzaMe\Helpers\Http\Resources;

class ResponseResource extends BaseJsonResource
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
            $this->mergeWhenNotNull('error')
        ];
    }
}
