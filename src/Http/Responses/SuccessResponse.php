<?php

namespace OnzaMe\Helpers\Http\Responses;

class SuccessResponse extends \Illuminate\Http\JsonResponse
{
    public function __construct(string $title, string $description, int $status = 200)
    {
        $data = [
            'success' => [
                'message' => [
                    'title' => $title,
                    'description' => $description
                ]
            ]
        ];

        parent::__construct($data, $status);
    }
}
