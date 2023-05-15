<?php

if (!function_exists('camelToSnake')) {
    function camelToSnake($input)
    {
        if (is_object($input)) {
            $input = (array)$input;
        }

        if (is_array($input)) {
            foreach ($input as $k => $item) {
                $oldKey = $k;
                $newKey = strtolower(preg_replace('/(?<!^)[A-Z]/', '_$0', $k));
                if (is_array($item) || is_object($item)) {
                    $item = camelToSnake($item);
                }
                $input[$newKey] = $item;
                unset($input['token']);
                if ($newKey != $oldKey) {
                    unset($input[$oldKey]);
                }
            }
        }
        return $input;
    }
}

if (!function_exists('snakeToCamel')) {
    function snakeToCamel($input)
    {
        if (is_object($input)) {
            $input = (array)$input;
        }

        if (is_array($input)) {
            foreach ($input as $k => $item) {
                $oldKey = $k;
                $newKey = lcfirst(str_replace(' ', '', ucwords(str_replace('_', ' ', $k))));
                if (is_array($item) || is_object($item)) {
                    $item = snakeToCamel($item);
                }
                $input[$newKey] = $item;
                if ($newKey != $oldKey) {
                    unset($input[$oldKey]);
                }
            }
        }
        return $input;
    }
}
