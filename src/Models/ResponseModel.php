<?php


namespace OnzaMe\Helpers\Models;


use OnzaMe\Helpers\Models\Contracts\ResponseModelContract;

class ResponseModel implements ResponseModelContract
{
    protected array $data;

    public function __construct(array $data = [])
    {
        $this->data = $data;
    }

    public function __get($name)
    {
        if (isset($this->data[$name])) {
            return $this->data[$name];
        }
        return null;
    }

    public function toArray(): array
    {
        return $this->data;
    }

    public static function make(array $data = []): self
    {
        return new self($data);
    }
}
