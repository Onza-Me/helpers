<?php


namespace OnzaMe\Helpers\Exceptions;

class PermissionDeniedException extends UnproccessableHttpRequestException
{
    public function __construct(string $title, string $description)
    {
        parent::__construct($title, $description, [], 403);
    }
}
