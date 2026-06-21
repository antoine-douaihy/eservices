<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
        {
            Schema::table('users', function (Blueprint $table) {
                // Nullable because citizens and super-admins don't belong to a specific local office
                $table->foreignId('office_id')->nullable()->constrained('offices')->nullOnDelete();
            });
        }

        public function down(): void
        {
            Schema::table('users', function (Blueprint $table) {
                $table->dropForeign(['office_id']);
                $table->dropColumn('office_id');
            });
        }
};
