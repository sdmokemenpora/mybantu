<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    use HasFactory;

    protected $table = 'transactions';

    protected $fillable = [
        'id_transaksi',
        'tanggal_transaksi',
        'user_id',
        'worker_id',
        'tipe_pekerjaan',
        'jumlah_qty',
        'date_from',
        'date_to',
        'no_hp',
        'alamat_pemesan',
        'kabupaten',
        'provinsi',
        'tanggal_kerja',
        'konfirmasi',
        'status_bekerja',
        'status_selesai',
        'status_pembayaran',
        'snap_token',
        'payment_type',
        'status_dana_terkirim',
        'alasan_cancel',
        'bank_tujuan',
        'no_rekening',
        'nama_rekening',
        'fee_platform',
        'nominal_cair',
        'waktu_cair',
        'rating',
    ];

    public function worker()
    {
        return $this->belongsTo(Worker::class, 'worker_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function ratingData()
    {
        return $this->hasOne(Rating::class, 'id_transaksi', 'id_transaksi');
    }
}
