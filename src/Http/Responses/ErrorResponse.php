<?php

namespace OnzaMe\Helpers\Http\Responses;

class ErrorResponse extends \Illuminate\Http\JsonResponse
{
    public function __construct(string $title, string $description, array $fields = [], int $status = 422)
    {
        $data = [
            'error' => [
                'message' => [
                    'title' => $title,
                    'description' => $description
                ]
            ],
            'fields' => $fields
        ];

        parent::__construct($data, $status);
    }

    public function addField($key, $value)
    {
        $data = $this->getData();

        if(isset($data['fields']))
            $data['fields'][$key][] = $value;
        else {
            $data['fields'][$key] = [];
            $data['fields'][$key][] = $value;
        }
    }

    public function exists()
    {
        $data = $this->getData();

        return count($data['fields']);
    }
}
