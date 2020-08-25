<?php

namespace OnzaMe\Helpers\Exceptions;

class UnproccessableHttpRequestException extends HttpRequestException
{
    public function __construct(string $title = 'Данные не прошли проверку', string $description = 'Введите корректные данные', array $fields = [], int $status = 422)
    {
        parent::__construct($title, $description, $fields, $status);
    }
}
