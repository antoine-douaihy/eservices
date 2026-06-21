<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {

    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // For Google Authenticator (TOTP)
            $table->string('two_factor_secret')->nullable()->after('role');

            // For email code 2FA
            $table->string('two_factor_email_code')->nullable()->after('two_factor_secret');
            $table->timestamp('two_factor_code_expires_at')->nullable()->after('two_factor_email_code');

            // Whether 2FA is active for this user
            $table->boolean('two_factor_enabled')->default(false)->after('two_factor_code_expires_at');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['two_factor_secret','two_factor_email_code','two_factor_code_expires_at','two_factor_enabled']);
        });
    }
};

