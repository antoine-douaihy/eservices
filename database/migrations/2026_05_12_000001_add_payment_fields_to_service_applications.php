<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('service_applications', function (Blueprint $table) {
            // Links to the CitizenRequest used for payment (null for free services)
            $table->foreignId('citizen_request_id')->nullable()->after('submitted_at')
                  ->constrained('citizen_requests')->nullOnDelete();
            // For free services that bypass CitizenRequest
            $table->string('certificate_path')->nullable()->after('citizen_request_id');
            $table->timestamp('approved_at')->nullable()->after('certificate_path');
        });
    }

    public function down(): void
    {
        Schema::table('service_applications', function (Blueprint $table) {
            $table->dropConstrainedForeignId('citizen_request_id');
            $table->dropColumn(['certificate_path', 'approved_at']);
        });
    }
};
