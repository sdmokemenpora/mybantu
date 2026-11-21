<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Admin extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $table = 'admins';

    protected $fillable = [
        'name',
        'email',
        'password',
        'phone_number',
        'role',
        'status',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'password' => 'hashed',
        ];
    }

    /**
     * Cek apakah admin aktif
     */
    public function isActive(): bool
    {
        return empty($this->status) || strtoupper(trim($this->status)) === 'AKTIF';
    }

    /**
     * Cek apakah Super Admin
     */
    public function isSuperAdmin(): bool
    {
        return strtolower($this->role) === 'super admin';
    }
}

