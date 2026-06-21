<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Extends encryption-at-rest to the citizen's submitted full_name and
 * email on each service request — the data tied to a specific
 * application, as opposed to their account-level name/email. This is
 * the data that certificates and payment receipts are generated from,
 * so it's encrypted in the database and only decrypted (automatically,
 * via the model's `encrypted` cast) at the moment a document is built.
 */
return new class extends Migration
{
    private array $targets = [
        'citizen_requests' => ['full_name', 'email'],
        'service_applications' => ['full_name', 'email'],
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
                            if ($value === null || $value === '' || $this->isAlreadyEncrypted($value)) {
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
