<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Encrypts sensitive personal-data columns (phone, address, notes) at rest
 * using AES-256 (Laravel's default Crypt cipher). Widens the affected
 * columns to TEXT first since encrypted ciphertext is significantly
 * longer than the original plaintext, then encrypts any existing
 * plaintext values in place. Values that are already encrypted
 * (idempotent re-run / fresh installs) are left untouched.
 */
return new class extends Migration
{
    /** Table => [columns to encrypt]. */
    private array $targets = [
        'users' => ['phone', 'address'],
        'citizen_requests' => ['phone', 'address', 'notes'],
        'service_applications' => ['phone', 'address', 'notes'],
    ];

    public function up(): void
    {
        foreach ($this->targets as $table => $columns) {
            if (!Schema::hasTable($table)) {
                continue;
            }

            Schema::table($table, function (Blueprint $blueprint) use ($table, $columns) {
                foreach ($columns as $column) {
                    if (Schema::hasColumn($table, $column)) {
                        $blueprint->text($column)->nullable()->change();
                    }
                }
            });

            $existingColumns = array_filter($columns, fn ($c) => Schema::hasColumn($table, $c));
            if (empty($existingColumns)) {
                continue;
            }

            DB::table($table)->select(array_merge(['id'], $existingColumns))
                ->orderBy('id')
                ->chunkById(200, function ($rows) use ($table, $existingColumns) {
                    foreach ($rows as $row) {
                        $updates = [];
                        foreach ($existingColumns as $column) {
                            $value = $row->{$column};
                            if ($value === null || $value === '') {
                                continue;
                            }
                            if ($this->isAlreadyEncrypted($value)) {
                                continue;
                            }
                            $updates[$column] = Crypt::encryptString($value);
                        }
                        if (!empty($updates)) {
                            DB::table($table)->where('id', $row->id)->update($updates);
                        }
                    }
                });
        }
    }

    public function down(): void
    {
        foreach ($this->targets as $table => $columns) {
            if (!Schema::hasTable($table)) {
                continue;
            }

            $existingColumns = array_filter($columns, fn ($c) => Schema::hasColumn($table, $c));
            if (empty($existingColumns)) {
                continue;
            }

            DB::table($table)->select(array_merge(['id'], $existingColumns))
                ->orderBy('id')
                ->chunkById(200, function ($rows) use ($table, $existingColumns) {
                    foreach ($rows as $row) {
                        $updates = [];
                        foreach ($existingColumns as $column) {
                            $value = $row->{$column};
                            if ($value === null || $value === '') {
                                continue;
                            }
                            try {
                                $updates[$column] = Crypt::decryptString($value);
                            } catch (\Throwable $e) {
                                // Already plaintext — leave as-is.
                            }
                        }
                        if (!empty($updates)) {
                            DB::table($table)->where('id', $row->id)->update($updates);
                        }
                    }
                });
        }
    }

    private function isAlreadyEncrypted(string $value): bool
    {
        try {
            Crypt::decryptString($value);
            return true;
        } catch (\Throwable $e) {
            return false;
        }
    }
};
