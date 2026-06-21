<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('offices', function (Blueprint $table) {
            $table->string('opening_time')->nullable()->after('email');
            $table->string('closing_time')->nullable()->after('opening_time');
            $table->string('working_days')->nullable()->after('closing_time');
        });
    }

    public function down(): void
    {
        Schema::table('offices', function (Blueprint $table) {
            $table->dropColumn(['opening_time', 'closing_time', 'working_days']);
        });
    }
};
