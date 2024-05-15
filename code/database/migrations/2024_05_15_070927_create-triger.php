<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        //
        DB::unprepared('
            CREATE TRIGGER personal_access_tokens_login_trigger AFTER INSERT ON `personal_access_tokens`
            FOR EACH ROW
            BEGIN
            INSERT INTO `personal_access_token_logs` (`personal_access_token_id`, `tokenable_type`,`tokenable_id`, `name`, `token`, `abilities`, `login_time`)
            SELECT `id`,`tokenable_type` ,`tokenable_id` ,`name`, `token`, `abilities`,`created_at`
            FROM `personal_access_tokens`
            ORDER BY `id` DESC
            LIMIT 1;
            END;
            CREATE TRIGGER personal_access_tokens_use_trigger AFTER UPDATE ON `personal_access_tokens`
            FOR EACH ROW
            BEGIN
            UPDATE `personal_access_token_logs` SET `last_used_at` = NEW.last_used_at
            WHERE `personal_access_token_id` = NEW.id;
            END;
            CREATE TRIGGER personal_access_tokens_delete_trigger BEFORE DELETE ON `personal_access_tokens` FOR EACH ROW BEGIN
            UPDATE `personal_access_token_logs` SET  `deleted_at` = NOW()
            WHERE  `personal_access_token_id` = OLD.id;
            END;
        ');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
        DB::unprepared('DROP TRIGGER IF EXISTS personal_access_tokens_login_trigger');
        DB::unprepared('DROP TRIGGER IF EXISTS personal_access_tokens_use_trigger');
        DB::unprepared('DROP TRIGGER IF EXISTS personal_access_tokens_delete_trigger');
    }
};
