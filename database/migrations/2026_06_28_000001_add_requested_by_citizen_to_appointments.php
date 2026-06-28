<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('appointments', function (Blueprint $table) {
            $table->boolean('requested_by_citizen')->default(false)->after('reminder_sent');
            $table->text('citizen_notes')->nullable()->after('requested_by_citizen');
        });
    }

    public function down(): void
    {
        Schema::table('appointments', function (Blueprint $table) {
            $table->dropColumn(['requested_by_citizen', 'citizen_notes']);
        });
    }
};
