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
        // Drop the obsolete foreign key referencing transactions_header
        $fks = DB::select("
            SELECT CONSTRAINT_NAME
            FROM INFORMATION_SCHEMA.KEY_COLUMN_USAGE
            WHERE TABLE_SCHEMA = DATABASE() 
              AND TABLE_NAME = 'ratings' 
              AND CONSTRAINT_NAME = 'ratings_ibfk_1'
        ");

        if (!empty($fks)) {
            Schema::table('ratings', function (Blueprint $table) {
                $table->dropForeign('ratings_ibfk_1');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // No need to restore the broken reference to the empty/legacy transactions_header table
    }
};

