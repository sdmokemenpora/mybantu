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
        // 1. Tambah foto_path ke tabel users
        if (Schema::hasTable('users') && !Schema::hasColumn('users', 'foto_path')) {
            Schema::table('users', function (Blueprint $table) {
                $table->string('foto_path')->nullable()->after('alamat_lengkap');
            });
        }

        // 2. Tambah description ke tabel workers
        if (Schema::hasTable('workers') && !Schema::hasColumn('workers', 'description')) {
            Schema::table('workers', function (Blueprint $table) {
                $table->text('description')->nullable()->after('tipe_pekerjaan');
            });
        }

        // 3. Tambah kolom informasi pemesan dan periode tanggal ke tabel transactions
        if (Schema::hasTable('transactions')) {
            Schema::table('transactions', function (Blueprint $table) {
                if (!Schema::hasColumn('transactions', 'date_from')) {
                    $table->date('date_from')->nullable()->after('jumlah_qty');
                }
                if (!Schema::hasColumn('transactions', 'date_to')) {
                    $table->date('date_to')->nullable()->after('date_from');
                }
                if (!Schema::hasColumn('transactions', 'no_hp')) {
                    $table->string('no_hp')->nullable()->after('date_to');
                }
                if (!Schema::hasColumn('transactions', 'alamat_pemesan')) {
                    $table->text('alamat_pemesan')->nullable()->after('no_hp');
                }
                if (!Schema::hasColumn('transactions', 'kabupaten')) {
                    $table->string('kabupaten')->nullable()->after('alamat_pemesan');
                }
                if (!Schema::hasColumn('transactions', 'provinsi')) {
                    $table->string('provinsi')->nullable()->after('kabupaten');
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('users') && Schema::hasColumn('users', 'foto_path')) {
            Schema::table('users', function (Blueprint $table) {
                $table->dropColumn('foto_path');
            });
        }

        if (Schema::hasTable('workers') && Schema::hasColumn('workers', 'description')) {
            Schema::table('workers', function (Blueprint $table) {
                $table->dropColumn('description');
            });
        }

        if (Schema::hasTable('transactions')) {
            Schema::table('transactions', function (Blueprint $table) {
                $columns = ['date_from', 'date_to', 'no_hp', 'alamat_pemesan', 'kabupaten', 'provinsi'];
                foreach ($columns as $column) {
                    if (Schema::hasColumn('transactions', $column)) {
                        $table->dropColumn($column);
                    }
                }
            });
        }
    }
};

