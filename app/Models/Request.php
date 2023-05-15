<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property string $method
 * @property string $url
 * @property string $label
 * @property string $data
 * @property string|int $user_id
 */
class Request extends Model
{
    use HasFactory;

    protected $fillable = [
        'method',
        'url',
        'label',
        'user_id'
    ];
}
