<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('local_payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('citizen_request_id')->constrained()->onDelete('cascade');
            $table->enum('method', ['wish', 'omt']);
            $table->decimal('amount_usd', 10, 2);
            $table->string('account_details');
            $table->string('reference_number')->nullable();
            $table->enum('status', ['pending', 'confirmed'])->default('pending');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('local_payments');
    }
};
