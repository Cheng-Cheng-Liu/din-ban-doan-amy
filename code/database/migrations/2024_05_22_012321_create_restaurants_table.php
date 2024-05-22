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
        Schema::create('restaurants', function (Blueprint $table) {
            $table->id();
            $table->string('name',255);
            $table->string('tag',255);
            $table->string('phone',15);
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
        Schema::dropIfExists('restaurants');
    }
};
