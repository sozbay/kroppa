<?php

namespace App\Models;

use App\Casts\Json;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property string|int $user_id
 */
class RequestHistory extends Model
{
    use HasFactory;

    protected $table = 'request_history';

    protected $casts = [
        'request_data' => Json::class,
        'response_data' => Json::class
    ];

    protected $fillable = [
        'user_id',
        'request_id',
        'request_data',
        'response_data',
        'response_code'
    ];

    public function request(): BelongsTo
    {
        return $this->belongsTo(Request::class);
    }
}
