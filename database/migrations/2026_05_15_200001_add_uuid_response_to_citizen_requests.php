<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('citizen_requests', function (Blueprint $table) {
            $table->uuid('uuid')->nullable()->unique()->after('id');
            $table->string('response_document')->nullable()->after('uploaded_document');
            $table->text('response_note')->nullable()->after('response_document');
        });

        // Back-fill UUIDs for existing rows
        \DB::table('citizen_requests')->whereNull('uuid')->orderBy('id')->each(function ($row) {
            \DB::table('citizen_requests')->where('id', $row->id)->update(['uuid' => Str::uuid()]);
        });
    }

    public function down(): void
    {
        Schema::table('citizen_requests', function (Blueprint $table) {
            $table->dropColumn(['uuid', 'response_document', 'response_note']);
        });
    }
};
