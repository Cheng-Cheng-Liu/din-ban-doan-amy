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
        Schema::create('comment_logs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger("comment_id");
            $table->foreign('comment_id')->references('id')->on('comments');
            $table->unsignedBigInteger("user_id");
            $table->foreign('user_id')->references('id')->on('users');
            $table->unsignedBigInteger("restaurant_id");
            $table->foreign('restaurant_id')->references('id')->on('restaurants');
            $table->text('description');
            $table->tinyInteger('score');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('comment_logs');
    }
};
