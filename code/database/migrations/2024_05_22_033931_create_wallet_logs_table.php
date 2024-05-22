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
        Schema::create('wallet_logs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger("user_id");
            $table->foreign('user_id')->references('id')->on('users');
            $table->unsignedBigInteger("wallet_id");
            $table->foreign('wallet_id')->references('id')->on('wallets');
            $table->unsignedBigInteger("order_id");
            $table->foreign('order_id')->references('id')->on('orders');
            $table->integer('amount');
            $table->integer('balance');
            $table->tinyInteger('status');
            $table->string('remark',255);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('wallet_logs');
    }
};
