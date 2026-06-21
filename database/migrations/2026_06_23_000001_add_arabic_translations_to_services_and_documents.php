<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('services', function (Blueprint $table) {
            if (!Schema::hasColumn('services', 'name_ar')) {
                $table->string('name_ar')->nullable()->after('name');
            }
            if (!Schema::hasColumn('services', 'description_ar')) {
                $table->text('description_ar')->nullable()->after('description');
            }
        });

        Schema::table('required_documents', function (Blueprint $table) {
            if (!Schema::hasColumn('required_documents', 'name_ar')) {
                $table->string('name_ar')->nullable()->after('name');
            }
        });
    }

    public function down(): void
    {
        Schema::table('services', function (Blueprint $table) {
            $table->dropColumn(['name_ar', 'description_ar']);
        });

        Schema::table('required_documents', function (Blueprint $table) {
            $table->dropColumn('name_ar');
        });
    }
};
