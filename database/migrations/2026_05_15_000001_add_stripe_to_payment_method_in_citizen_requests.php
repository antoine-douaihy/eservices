<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("ALTER TABLE citizen_requests MODIFY COLUMN payment_method ENUM('fiat','crypto','wish','omt','stripe') NULL");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE citizen_requests MODIFY COLUMN payment_method ENUM('fiat','crypto','wish','omt') NULL");
    }
};
