<?php

namespace OnzaMe\Helpers\Http\Responses;

use Illuminate\Http\JsonResponse;
use OnzaMe\Helpers\Http\Resources\ResponseResource;
use OnzaMe\Helpers\Models\ResponseModel;

class SuccessResponse extends JsonResponse
{
    public function __construct(string $title, string $description, int $status = 200, $data = null)
    {
        $responseModel = ResponseModel::make([
            'success' => [
                'message' => [
                    'title' => $title,
                    'description' => $description
                ]
            ],
            'data' => $data
        ]);

        parent::__construct(ResponseResource::make($responseModel), $status);
    }

    public static function make(string $title, string $description, int $status = 200, $data = null): self
    {
        return new self($title, $description, $status, $data);
    }
}
