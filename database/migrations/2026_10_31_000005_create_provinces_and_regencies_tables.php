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
        if (!Schema::hasTable('provinces')) {
            Schema::create('provinces', function (Blueprint $table) {
                $table->id();
                $table->string('name', 100)->unique();
                $table->string('slug', 100)->nullable();
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('regencies')) {
            Schema::create('regencies', function (Blueprint $table) {
                $table->id();
                $table->foreignId('province_id')->constrained('provinces')->onDelete('cascade');
                $table->string('name', 100);
                $table->timestamps();

                $table->index(['province_id', 'name']);
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('regencies');
        Schema::dropIfExists('provinces');
    }
};

