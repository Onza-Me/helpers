<?php

namespace OnzaMe\Helpers\Exceptions;

class InvalidIDInHttpRequestException extends HttpRequestException
{
    public function __construct(string $title = 'Идентификатор(ID) не верного формата', string $description = 'Введите корректные данные', array $fields = [], int $status = 422)
    {
        parent::__construct($title, $description, $fields, $status);
    }
}
