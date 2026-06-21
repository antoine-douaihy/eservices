<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('crypto_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('citizen_request_id')->constrained()->onDelete('cascade');
            $table->enum('currency', ['BTC', 'ETH']);
            $table->decimal('amount_usd', 10, 2);
            $table->decimal('amount_crypto', 18, 8);
            $table->decimal('crypto_price_usd', 12, 2);
            $table->string('wallet_address');
            $table->string('tx_hash')->nullable();
            $table->enum('status', ['pending', 'confirmed', 'expired'])->default('pending');
            $table->timestamp('expires_at');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('crypto_transactions');
    }
};
