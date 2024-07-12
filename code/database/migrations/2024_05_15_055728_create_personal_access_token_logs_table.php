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
        Schema::create('personal_access_token_logs', function (Blueprint $table) {
            $table->id();
            $table->bigInteger("personal_access_token_id");
            $table->string('tokenable_type', 255);
            $table->bigInteger("tokenable_id");
            $table->string('name', 255);
            $table->string('token', 64);
            $table->text('abilities')->nullable();
            $table->dateTime('login_time', $precision = 0)->nullable();
            $table->dateTime('last_used_at', $precision = 0)->nullable();
            $table->dateTime('deleted_at', $precision = 0)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('personal_access_token_logs');
    }
};
