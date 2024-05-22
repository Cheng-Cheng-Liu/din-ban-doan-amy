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
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger("user_id");
            $table->foreign('user_id')->references('id')->on('users');
            $table->unsignedBigInteger("restaurant_id");
            $table->foreign('restaurant_id')->references('id')->on('restaurants');
            $table->string('another_id',255);
            $table->string('choose_payment',255);
            $table->integer('amount');
            $table->tinyInteger('status');
            $table->string('remark',255);
            $table->dateTime('pick_up_time', $precision = 0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
