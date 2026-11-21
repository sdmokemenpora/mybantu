<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (Schema::hasTable('transactions')) {
            // Ubah tipe kolom konfirmasi dari enum('Yes','No') menjadi varchar(20) agar dapat menampung 'Rejected'
            DB::statement("ALTER TABLE transactions MODIFY COLUMN konfirmasi VARCHAR(20) NOT NULL DEFAULT 'No'");
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('transactions')) {
            DB::statement("ALTER TABLE transactions MODIFY COLUMN konfirmasi ENUM('Yes', 'No') NOT NULL DEFAULT 'No'");
        }
    }
};

