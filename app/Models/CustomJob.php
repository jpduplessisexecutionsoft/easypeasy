<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CustomJob extends Model
{
    protected $fillable = ['class', 'method', 'params', 'status', 'output', 'attempts', 'last_attempt_at', 'pid'];
    protected $casts = [
        'params' => 'array',
    ];
}
