<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('citizen_requests', function (Blueprint $table) {
            $table->string('full_name')->nullable()->after('office_id');
            $table->string('phone', 30)->nullable()->after('full_name');
            $table->string('email')->nullable()->after('phone');
            $table->string('address')->nullable()->after('email');
            $table->timestamp('submitted_at')->nullable()->after('address');
        });

        DB::statement("ALTER TABLE citizen_requests MODIFY COLUMN status ENUM('pending','pending_payment','in_review','approved','rejected') NOT NULL DEFAULT 'pending'");
    }

    public function down(): void
    {
        Schema::table('citizen_requests', function (Blueprint $table) {
            $table->dropColumn(['full_name', 'phone', 'email', 'address', 'submitted_at']);
        });

        DB::statement("ALTER TABLE citizen_requests MODIFY COLUMN status ENUM('pending','in_review','approved','rejected') NOT NULL DEFAULT 'pending'");
    }
};
