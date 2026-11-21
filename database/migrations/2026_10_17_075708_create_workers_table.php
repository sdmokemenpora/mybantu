<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('workers', function (Blueprint $table) {
            $table->id();
            $table->string('fullname');
            $table->string('email')->unique();
            $table->string('password');
            $table->string('nik', 16);
            $table->string('phone_number');
            $table->string('sex')->nullable();
            $table->date('birth_date')->nullable();
            $table->string('provinsi');
            $table->string('kabupaten');
            $table->text('alamat_lengkap');
            $table->string('foto_path')->nullable();
            $table->string('ktp_path')->nullable();
            $table->string('skck_path')->nullable();
            $table->string('tipe_pekerjaan')->nullable();
            $table->string('status_data')->default('belum terverifikasi');
            $table->string('status_worker')->default('AKTIF');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('workers');
    }
};
