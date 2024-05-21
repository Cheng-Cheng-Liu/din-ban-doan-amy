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
        Schema::create('favorites', function (Blueprint $table) {
            $table->id();
            $table->bigInteger("restaurant_id");
            $table->foreign('restaurant_id')->references('id')->on('restaurants');
            $table->bigInteger("user_id");
            $table->foreign('user_id')->references('id')->on('users');
            $table->timestamps();
        });

        Schema::create('restaurants', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('phone');
            $table->dateTime('opening_time', $precision = 0);
            $table->dateTime('closing_time', $precision = 0);
            $table->string('rest_day');
            $table->tinyInteger('status');
            $table->float('avg_score', 2, 1);
            $table->integer('total_comments_count');
            $table->integer('priority');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('favorites');
        Schema::dropIfExists('restaurants');
    }
};
