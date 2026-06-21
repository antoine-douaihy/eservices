<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->boolean('requires_first_login_otp')->default(false)->after('status');
            $table->string('first_login_otp_code', 6)->nullable()->after('requires_first_login_otp');
            $table->timestamp('first_login_otp_expires_at')->nullable()->after('first_login_otp_code');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['requires_first_login_otp', 'first_login_otp_code', 'first_login_otp_expires_at']);
        });
    }
};
