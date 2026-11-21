<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SecurityLog extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'event_type',
        'endpoint',
        'guard',
        'email',
        'ip_address',
        'user_agent',
        'reason',
        'attempts_in_window',
        'created_at',
    ];

    protected $casts = [
        'attempts_in_window' => 'integer',
        'created_at' => 'datetime',
    ];
}
