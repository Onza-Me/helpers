<?php


namespace OnzaMe\Helpers\Models\Contracts;


interface ResponseModelContract
{
    public static function make(array $data = []): self;

    public function __construct(array $data = []);

    public function __get($name);

    public function toArray():array;
}
