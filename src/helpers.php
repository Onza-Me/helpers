<?php

if (!function_exists('array_mapper')) {
    function array_mapper (array $array)
    {
        return \OnzaMe\Helpers\ArrayMapper::create($array);
    }
}
