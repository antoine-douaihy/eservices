<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // MySQL: alter enum to include USDT
        DB::statement("ALTER TABLE crypto_transactions MODIFY COLUMN currency ENUM('BTC','ETH','USDT') NOT NULL");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE crypto_transactions MODIFY COLUMN currency ENUM('BTC','ETH') NOT NULL");
    }
};
