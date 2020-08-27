<?php

if (!function_exists('array_mapper')) {
    function array_mapper (array $array)
    {
        return \OnzaMe\Helpers\ArrayMapper::create($array);
    }
}

if (!function_exists('get_converted_json_from_request')) {
    function get_converted_json_from_request (\Illuminate\Http\Request $request, string $key)
    {
        $json = $request->get($key) ?? [];
        if (!is_string($json)) {
            return $json;
        }

        try {
            return json_decode($json);
        } catch (\Exception $ex) {
            return $json;
        }
    }
}
