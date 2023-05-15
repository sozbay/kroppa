<?php

namespace App\Casts;

use Illuminate\Contracts\Database\Eloquent\CastsAttributes;
use Illuminate\Database\Eloquent\Model;

class Json implements CastsAttributes
{
    /**
     * Cast the given value.
     *
     * @param Model $model
     * @param string $key
     * @param mixed $value
     * @param array $attributes
     * @return array|null
     * @noinspection PhpUnused
     */
    public function get($model, string $key, $value, array $attributes): ?array
    {
        return json_decode($value, true);
    }

    /**
     * Prepare the given value for storage.
     *
     * @param Model $model
     * @param string $key
     * @param array $value
     * @param array $attributes
     * @return string
     * @noinspection PhpUnused
     */
    public function set($model, string $key, $value, array $attributes): string
    {
        return json_encode($value);
    }
}
