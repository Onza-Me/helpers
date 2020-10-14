<?php

namespace OnzaMe\Helpers\Models;

use Illuminate\Database\Eloquent\Model;

class ExternalRequest extends Model
{
    protected $guarded = ['id'];

    protected $casts = [
        'headers' => 'array',
        'response' => 'array'
    ];
}
