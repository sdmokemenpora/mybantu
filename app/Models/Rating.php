<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Rating extends Model
{
    use HasFactory;

    protected $table = 'ratings';

    protected $fillable = [
        'id_transaksi',
        'user_id',
        'worker_id',
        'rating',
        'review',
    ];

    // Relasi ke Transaksi
    public function transaction()
    {
        return $this->belongsTo(Transaction::class, 'id_transaksi', 'id_transaksi');
    }

    // Relasi ke Worker yang dinilai
    public function worker()
    {
        return $this->belongsTo(Worker::class, 'worker_id');
    }

    // Relasi ke User yang memberi rating
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
