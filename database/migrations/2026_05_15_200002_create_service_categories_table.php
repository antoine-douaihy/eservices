<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('service_categories', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->string('icon')->nullable()->default('bi-grid');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::table('services', function (Blueprint $table) {
            $table->foreignId('category_id')->nullable()->constrained('service_categories')->nullOnDelete()->after('office_id');
            $table->text('required_documents')->nullable()->after('description');
        });
    }

    public function down(): void
    {
        Schema::table('services', function (Blueprint $table) {
            $table->dropForeign(['category_id']);
            $table->dropColumn(['category_id', 'required_documents']);
        });
        Schema::dropIfExists('service_categories');
    }
};
