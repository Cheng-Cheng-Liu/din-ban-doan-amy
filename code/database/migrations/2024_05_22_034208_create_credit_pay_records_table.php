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
        Schema::create('credit_pay_records', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger("wallet_log_id");
            $table->foreign('wallet_log_id')->references('id')->on('wallet_logs');
            $table->string('payment_type',15);
            $table->integer('merchant_id');
            $table->string('merchant_trade_no',255);
            $table->string('card_no',255);
            $table->integer('amount');
            $table->string('trade_desc',255);
            $table->string('item_name',255);
            $table->string('choose_payment',255);
            $table->string('check_mac_value',255);
            $table->tinyInteger('status');
            $table->string('remark',255);
            $table->dateTime('payment_date', $precision = 0);
            $table->dateTime('trade_date', $precision = 0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('credit_pay_records');
    }
};
