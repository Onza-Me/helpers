<?php

namespace OnzaMe\Helpers\Exceptions;

class ModelNotFoundHttpRequestException extends HttpRequestException
{
    public function __construct(string $title = 'Запись не найдена', string $description = 'Не удалось найти запись', array $fields = [], int $status = 404)
    {
        parent::__construct($title, $description, $fields, $status);
    }
}
