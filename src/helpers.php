<?php
use Illuminate\Support\Facades\Storage;

if (!function_exists('remove_disk_prefix_path')) {
    function remove_disk_prefix_path(string $absolutePath, string $disk = 'public'): string
    {
        return str_replace(Storage::disk($disk)->getDriver()->getAdapter()->getPathPrefix(), '', $absolutePath);
    }
}

if (!function_exists('unset_if_exists')) {
    function unset_if_exists(array &$array, $key)
    {
        if (is_array($key)) {
            foreach ($key as $value) {
                unset_if_exists($array, $value);
            }
            return;
        }

        if (!isset($array[$key])) {
            return;
        }
        unset($array[$key]);
    }
}

if (!function_exists('array_mapper')) {
    function array_mapper (array $array)
    {
        return \OnzaMe\Helpers\ArrayMapper::create($array);
    }
}

if (!function_exists('is_json')) {
    function is_json (string $json): bool
    {
        try {
            $result = json_decode($json);
            if (is_null($result) && $json !== 'null') {
                return false;
            }
            return true;
        } catch (\Exception $e) {
            return false;
        }
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
            return json_decode($json, true);
        } catch (\Exception $ex) {
            return $json;
        }
    }
}

if (!function_exists('is_boolean_var')) {
    function is_boolean_var ($var): bool
    {
        if (is_bool($var) || in_array($var, ['true', 'false'])) {
            return true;
        }

        return false;
    }
}

if (!function_exists('parse_boolean_var')) {
    function parse_boolean_var ($var): bool
    {
        if (is_bool($var)) {
            return $var;
        }
        if (in_array($var, ['true', 'false'])) {
            return json_decode($var);
        }
        return false;
    }
}
