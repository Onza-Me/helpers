<?php

namespace OnzaMe\Helpers\Exceptions;

use Exception;
use OnzaMe\Helpers\Http\Responses\ErrorResponse;

class HttpRequestException extends Exception
{
    protected  ErrorResponse $response;


    public function __construct(string $title = 'Ошибка', string $description = 'Не предвиденная ошибка', array $fields = [], int $status = 500)
    {
        $this->response = new ErrorResponse($title, $description, $fields, $status);

        parent::__construct($title."\n".$description);
    }

    public function getResponse()
    {
        return $this->response;
    }
}
