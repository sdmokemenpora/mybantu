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
        if (Schema::hasTable('transactions')) {
            Schema::table('transactions', function (Blueprint $table) {
                if (!Schema::hasColumn('transactions', 'bank_tujuan')) {
                    $table->string('bank_tujuan', 50)->nullable()->after('status_dana_terkirim');
                }
                if (!Schema::hasColumn('transactions', 'no_rekening')) {
                    $table->string('no_rekening', 50)->nullable()->after('bank_tujuan');
                }
                if (!Schema::hasColumn('transactions', 'nama_rekening')) {
                    $table->string('nama_rekening', 100)->nullable()->after('no_rekening');
                }
                if (!Schema::hasColumn('transactions', 'fee_platform')) {
                    $table->decimal('fee_platform', 15, 2)->default(0)->after('nama_rekening');
                }
                if (!Schema::hasColumn('transactions', 'nominal_cair')) {
                    $table->decimal('nominal_cair', 15, 2)->default(0)->after('fee_platform');
                }
                if (!Schema::hasColumn('transactions', 'waktu_cair')) {
                    $table->timestamp('waktu_cair')->nullable()->after('nominal_cair');
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('transactions')) {
            Schema::table('transactions', function (Blueprint $table) {
                $columns = ['bank_tujuan', 'no_rekening', 'nama_rekening', 'fee_platform', 'nominal_cair', 'waktu_cair'];
                foreach ($columns as $column) {
                    if (Schema::hasColumn('transactions', $column)) {
                        $table->dropColumn($column);
                    }
                }
            });
        }
    }
};

