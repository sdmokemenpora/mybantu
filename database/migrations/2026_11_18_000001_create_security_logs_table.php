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
        Schema::create('security_logs', function (Blueprint $table) {
            $table->id();
            $table->string('event_type', 40)->index(); // failed_login, rate_limited, inactive_account, login_success
            $table->string('endpoint', 100);
            $table->string('guard', 30)->default('web');
            $table->string('email')->nullable()->index();
            $table->string('ip_address', 45)->index();
            $table->text('user_agent')->nullable();
            $table->string('reason')->nullable();
            $table->unsignedInteger('attempts_in_window')->default(1);
            $table->timestamp('created_at')->useCurrent()->index();

            $table->index(['ip_address', 'created_at']);
            $table->index(['email', 'created_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('security_logs');
    }
};
