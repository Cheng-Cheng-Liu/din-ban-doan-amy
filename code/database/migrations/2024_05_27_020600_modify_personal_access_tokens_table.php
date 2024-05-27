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
        //
        Schema::table('personal_access_tokens', function (Blueprint $table) {
            // 删除 'token' 列
            $table->dropColumn('token');

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
        Schema::table('personal_access_tokens', function (Blueprint $table) {
            // 如果需要，可以在此处定义回滚时的行为
            // 例如将 'token' 列改回 string 类型
            $table->string('token', 255)->change();
        });
    }
};
