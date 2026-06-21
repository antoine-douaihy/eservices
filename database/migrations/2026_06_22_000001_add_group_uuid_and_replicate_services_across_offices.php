<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

/**
 * Every service should be offered at every active office (i.e. every
 * municipality), not just the single office it happened to be created
 * under. This migration:
 *
 *   1. Adds a `group_uuid` column that links together all the
 *      per-office copies of what is conceptually "the same service"
 *      (used by the controller to keep edits/deletes in sync).
 *   2. Groups any existing services by name (services created before
 *      this change didn't have a group_uuid yet).
 *   3. For each group, creates a copy — including its required
 *      documents — at every active office that doesn't already have
 *      a service in that group.
 */
return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('services', 'group_uuid')) {
            Schema::table('services', function (Blueprint $table) {
                $table->string('group_uuid', 36)->nullable()->after('office_id')->index();
            });
        }

        // 1. Assign a shared group_uuid to existing services with the same name.
        $names = \DB::table('services')->whereNull('group_uuid')->pluck('name')->unique();
        foreach ($names as $name) {
            $uuid = (string) Str::uuid();
            \DB::table('services')->where('name', $name)->whereNull('group_uuid')->update(['group_uuid' => $uuid]);
        }

        // 2. Replicate each group to every active office that doesn't have it yet.
        $activeOfficeIds = \DB::table('offices')->where('is_active', true)->pluck('id');
        $groups = \DB::table('services')->select('group_uuid')->distinct()->whereNotNull('group_uuid')->pluck('group_uuid');

        foreach ($groups as $groupUuid) {
            $existing = \DB::table('services')->where('group_uuid', $groupUuid)->get();
            if ($existing->isEmpty()) {
                continue;
            }

            $template = $existing->first();
            $officeIdsWithService = $existing->pluck('office_id')->all();
            $missingOfficeIds = $activeOfficeIds->diff($officeIdsWithService);

            foreach ($missingOfficeIds as $officeId) {
                $newServiceId = \DB::table('services')->insertGetId([
                    'name'             => $template->name,
                    'slug'             => Str::slug($template->name) . '-' . Str::random(6),
                    'description'      => $template->description,
                    'price'            => $template->price,
                    'currency'         => $template->currency,
                    'processing_days'  => $template->processing_days,
                    'office_id'        => $officeId,
                    'category_id'      => $template->category_id ?? null,
                    'group_uuid'       => $groupUuid,
                    'is_active'        => $template->is_active,
                    'created_at'       => now(),
                    'updated_at'       => now(),
                ]);

                // Clone required documents from the template service.
                $docs = \DB::table('required_documents')->where('service_id', $template->id)->get();
                foreach ($docs as $doc) {
                    \DB::table('required_documents')->insert([
                        'service_id'   => $newServiceId,
                        'name'         => $doc->name,
                        'notes'        => $doc->notes,
                        'is_mandatory' => $doc->is_mandatory,
                        'sort_order'   => $doc->sort_order,
                        'created_at'   => now(),
                        'updated_at'   => now(),
                    ]);
                }
            }
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('services', 'group_uuid')) {
            Schema::table('services', function (Blueprint $table) {
                $table->dropColumn('group_uuid');
            });
        }
    }
};
