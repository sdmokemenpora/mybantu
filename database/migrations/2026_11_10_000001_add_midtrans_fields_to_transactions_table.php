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
        Schema::table('transactions', function (Blueprint $table) {
            if (!Schema::hasColumn('transactions', 'snap_token')) {
                $table->string('snap_token', 255)->nullable()->after('status_pembayaran');
            }
            if (!Schema::hasColumn('transactions', 'payment_type')) {
                $table->string('payment_type', 50)->nullable()->after('snap_token');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('transactions', function (Blueprint $table) {
            if (Schema::hasColumn('transactions', 'snap_token')) {
                $table->dropColumn('snap_token');
            }
            if (Schema::hasColumn('transactions', 'payment_type')) {
                $table->dropColumn('payment_type');
            }
        });
    }
};

