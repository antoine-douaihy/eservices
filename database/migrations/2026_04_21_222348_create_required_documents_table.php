<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('required_documents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('service_id')
                  ->constrained('services')
                  ->onDelete('cascade');
            $table->string('name');                          // e.g. "National ID", "Birth Certificate"
            $table->text('notes')->nullable();               // extra instructions for this document
            $table->boolean('is_mandatory')->default(true);  // mandatory vs optional
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('required_documents');
    }
};
