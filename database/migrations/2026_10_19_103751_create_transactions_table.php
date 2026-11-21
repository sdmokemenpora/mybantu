<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->string('id_transaksi'); // Kode unik transaksi
            $table->date('tanggal_transaksi');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade'); // Pembeli
            $table->foreignId('worker_id')->constrained('workers')->onDelete('cascade'); // Seller/Worker
            $table->string('tipe_pekerjaan'); // Harian / Mingguan
            $table->integer('jumlah_qty');
            $table->date('tanggal_kerja'); // Tanggal spesifik per row hasil looping
            $table->enum('konfirmasi', ['Yes', 'No'])->default('No');
            $table->string('status_bekerja')->nullable(); // "X" atau blank
            $table->string('status_selesai')->nullable(); // "X" atau blank
            $table->string('status_pembayaran')->default('Belum Bayar'); // Belum Bayar / Sudah Bayar
            $table->string('status_dana_terkirim')->default('Belum'); // Belum / Terkirim
            $table->integer('rating')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};