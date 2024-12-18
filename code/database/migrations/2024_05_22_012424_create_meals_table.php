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
        Schema::create('meals', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->id();
            $table->unsignedBigInteger("restaurant_id");
            $table->foreign('restaurant_id')->references('id')->on('restaurants');
            $table->string('another_id',255);
            $table->string('name',255);
            $table->integer('price');
            $table->integer('status');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('meals');
    }
};
