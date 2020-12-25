<?php

namespace OnzaMe\Helpers\Http\Responses;

use Illuminate\Http\JsonResponse;
use OnzaMe\Helpers\Models\ResponseModel;
use OnzaMe\Helpers\Http\Resources\ResponseResource;

class ErrorResponse extends JsonResponse
{
    public function __construct(string $title, string $description, array $fields = [], int $status = 422)
    {
        $responseModel = ResponseModel::make([
            'error' => [
                'message' => [
                    'title' => $title,
                    'description' => $description
                ],
                'fields' => $fields
            ]
        ]);


        parent::__construct(ResponseResource::make($responseModel), $status);
    }

    public static function make(string $title, string $description, array $fields = [], int $status = 422): self
    {
        return new self($title, $description, $fields, $status);
    }

    public function addField($key, $value)
    {
        $data = $this->getData(true);

        if (!isset($data['fields'])) {
            $data['fields'] = [];
        }
        if (!isset($data['fields'][$key])) {
            $data['fields'][$key] = [];
        }

        $data['fields'][$key][] = $value;

        $this->setData($data);
    }

    public function exists()
    {
        $data = $this->getData(true);

        return count($data['fields']);
    }
}
