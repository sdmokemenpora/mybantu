<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PriceWorker extends Model
{
    use HasFactory;

    protected $table = 'price_worker';
    public $timestamps = false; // Atur true jika ingin menggunakan created_at/updated_at

    protected $fillable = [
        'worker_id',
        'tipe_pekerjaan',
        'harga',
    ];

    public function worker()
    {
        return $this->belongsTo(Worker::class, 'worker_id');
    }
}