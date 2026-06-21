<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('citizen_request_ratings', function (Blueprint $table) {
            $table->text('office_response')->nullable()->after('comment');
            $table->timestamp('responded_at')->nullable()->after('office_response');
        });
    }

    public function down(): void
    {
        Schema::table('citizen_request_ratings', function (Blueprint $table) {
            $table->dropColumn(['office_response', 'responded_at']);
        });
    }
};
