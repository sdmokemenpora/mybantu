<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Worker extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $table = 'workers';

    protected $fillable = [
        'fullname',
        'email',
        'password',
        'nik',
        'phone_number',
        'sex',
        'birth_date',
        'provinsi',
        'kabupaten',
        'alamat_lengkap',
        'foto_path',
        'ktp_path',
        'skck_path',
        'tipe_pekerjaan',
        'description',
        'status_data',  
        'status_worker',
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
     * Cek apakah status data worker sudah terverifikasi
     */
    public function isVerified(): bool
    {
        return in_array(strtolower($this->status_data ?? ''), ['sudah terverifikasi', 'terverifikasi']);
    }

    /**
     * Relasi ke tabel price_worker (1 Worker bisa memiliki banyak baris harga tipe pekerjaan)
     */
    public function prices()
    {
        return $this->hasMany(PriceWorker::class, 'worker_id');
    }

    /**
     * Relasi ke tabel ratings (1 Worker bisa memiliki banyak ulasan & penilaian majikan)
     */
    public function ratings()
    {
        return $this->hasMany(Rating::class, 'worker_id');
    }
}
