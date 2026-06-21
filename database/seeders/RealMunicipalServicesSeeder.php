<?php

namespace Database\Seeders;

use App\Models\Office;
use App\Models\RequiredDocument;
use App\Models\Service;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

/**
 * Replaces the placeholder demo services with the 7 real Lebanese
 * municipal services transcribed from the official "دليل المواطن"
 * (Citizen's Guide) booklet — Ministry of Interior and Municipalities,
 * forms T-1, T-2, T-4/3, T-12, T-14, T-17, T-22.
 *
 * Each service is created once per active office (every municipality),
 * all copies linked by a shared group_uuid — matching the "every
 * municipality has every service" behavior added for staff/admin
 * service management. Names/descriptions/documents are stored in both
 * English and Arabic so the catalog displays correctly when a citizen
 * switches the site language.
 */
class RealMunicipalServicesSeeder extends Seeder
{
    public function run(): void
    {
        $existing = Service::where('name', 'Building Permit')->first();

        // Already seeded AND already has its Arabic translation — nothing to do.
        if ($existing && !empty($existing->name_ar)) {
            $this->command?->info('Real municipal services already seeded — skipping.');
            return;
        }

        // Already seeded but from before Arabic support was added —
        // backfill the translations on the existing rows instead of
        // creating duplicates.
        if ($existing) {
            $this->backfillTranslations();
            return;
        }

        // Deactivate the old placeholder services rather than deleting
        // them, so any historical citizen requests tied to them remain
        // intact — they just stop appearing in the live catalog.
        Service::whereNull('group_uuid')->update(['is_active' => false]);

        $offices = Office::where('is_active', true)->get();
        if ($offices->isEmpty()) {
            $this->command?->warn('No active offices found — run MunicipalityOfficeServiceSeeder first.');
            return;
        }

        $services = $this->serviceDefinitions();

        foreach ($services as $definition) {
            $groupUuid = (string) Str::uuid();

            foreach ($offices as $office) {
                $service = Service::create([
                    'name'            => $definition['name'],
                    'name_ar'         => $definition['name_ar'] ?? null,
                    'slug'            => Str::slug($definition['name']) . '-' . Str::random(6),
                    'description'     => $definition['description'],
                    'description_ar'  => $definition['description_ar'] ?? null,
                    'price'           => $definition['price'],
                    'currency'        => 'USD',
                    'processing_days' => $definition['processing_days'],
                    'office_id'       => $office->id,
                    'group_uuid'      => $groupUuid,
                    'is_active'       => true,
                ]);

                foreach ($definition['documents'] as $index => $doc) {
                    $name = $doc['name'] ?? $doc[0] ?? null;
                    if (!$name) {
                        continue;
                    }
                    RequiredDocument::create([
                        'service_id'   => $service->id,
                        'name'         => $name,
                        'name_ar'      => $doc['name_ar'] ?? null,
                        'is_mandatory' => $doc['mandatory'] ?? true,
                        'sort_order'   => $index,
                    ]);
                }
            }
        }

        $this->command?->info('Real municipal services seeded (with Arabic translations) across ' . $offices->count() . ' office(s), ' . count($services) . ' services each.');
    }

    /**
     * Backfill Arabic translations onto services/documents that were
     * already seeded by an earlier run of this seeder (before Arabic
     * support existed), instead of recreating them.
     */
    private function backfillTranslations(): void
    {
        $updatedServices = 0;
        $updatedDocs = 0;

        foreach ($this->serviceDefinitions() as $definition) {
            $matches = Service::where('name', $definition['name'])->get();

            foreach ($matches as $service) {
                $service->update([
                    'name_ar'        => $definition['name_ar'] ?? null,
                    'description_ar' => $definition['description_ar'] ?? null,
                ]);
                $updatedServices++;

                foreach ($definition['documents'] as $doc) {
                    $docName = $doc['name'] ?? $doc[0] ?? null;
                    if (!$docName || empty($doc['name_ar'] ?? null)) {
                        continue;
                    }
                    $updatedDocs += RequiredDocument::where('service_id', $service->id)
                        ->where('name', $docName)
                        ->update(['name_ar' => $doc['name_ar']]);
                }
            }
        }

        $this->command?->info("Backfilled Arabic translations on {$updatedServices} service row(s) and {$updatedDocs} document row(s).");
    }

    /** The 7 real municipal services, in English and Arabic. */
    private function serviceDefinitions(): array
    {
        return [
            [
                'name' => 'Building Permit',
                'name_ar' => 'ترخيص بالبناء',
                'description' => 'Official municipal permit required before starting new construction on a property (Decision 2761/1995, Article 2).',
                'description_ar' => 'رخصة بلدية رسمية مطلوبة قبل الشروع في أي بناء جديد على عقار (قرار وزير الأشغال العامة رقم ٢٧٦١ لعام ١٩٩٥، المادة ٢).',
                'price' => 150.00,
                'processing_days' => 30,
                'documents' => [
                    ['name' => 'Engineer Assignment Agreement', 'name_ar' => 'التكليف والاتفاقية بين المالك والمهندس المسؤول', 'mandatory' => true],
                    ['name' => "Engineer's Pledge & Joint Engineer Contracts", 'name_ar' => 'تعهد المهندس المسؤول وعقود المهندسين المشتركين', 'mandatory' => true],
                    ['name' => '5 Copies of Building Plans (Engineer-Certified)', 'name_ar' => 'خمس نسخ عن خرائط البناء موقعة من مهندس', 'mandatory' => true],
                    ['name' => 'Technical Inspection Approval', 'name_ar' => 'موافقة الدوائر الفنية المختصة (الكشف الفني)', 'mandatory' => true],
                    ['name' => 'Easement & Planning Certificate', 'name_ar' => 'إفادة ارتفاق وتخطيط', 'mandatory' => true],
                    ['name' => 'Property Valuation Certificate (price per sqm)', 'name_ar' => 'إفادة تخمين بالثمن البيعي للمتر المربع', 'mandatory' => true],
                    ['name' => 'Comprehensive Real Estate Certificate (max 3 months old)', 'name_ar' => 'إفادة عقارية شاملة لا يعود تاريخها لأكثر من ثلاثة أشهر', 'mandatory' => true],
                    ['name' => 'Area Verification Schedule', 'name_ar' => 'جدول تدقيق المساحات', 'mandatory' => true],
                ],
            ],
            [
                'name' => 'Renovation or Building Addition Permit',
                'name_ar' => 'ترخيص بالترميم أو إضافة بناء',
                'description' => 'Permit for renovating an existing structure or adding to an existing building (Form T-2). Requires proof of the existing building permit.',
                'description_ar' => 'رخصة لترميم بناء قائم أو إضافة بناء إليه (نموذج ط ٢)، تتطلب إثبات وجود رخصة بناء سابقة.',
                'price' => 100.00,
                'processing_days' => 21,
                'documents' => [
                    ['name' => 'Engineer Assignment Agreement', 'name_ar' => 'التكليف والاتفاقية بين المالك والمهندس المسؤول', 'mandatory' => true],
                    ['name' => "Engineer's Pledge (per Engineers' Syndicate form)", 'name_ar' => 'تعهد المهندس المسؤول وفقاً لنقابة المهندسين', 'mandatory' => true],
                    ['name' => 'Existing Building Permit Copy', 'name_ar' => 'رخصة البناء الموجودة أو نسخة طبق الأصل عنها', 'mandatory' => true],
                    ['name' => '5 Copies of Building Plans (Engineer-Certified)', 'name_ar' => 'خمس نسخ من خرائط البناء موقعة من المهندس', 'mandatory' => true],
                    ['name' => 'Easement & Planning Certificate', 'name_ar' => 'إفادة ارتفاق وتخطيط', 'mandatory' => true],
                    ['name' => 'Comprehensive Real Estate Certificate (max 3 months old)', 'name_ar' => 'إفادة عقارية شاملة لا يعود تاريخها لأكثر من ثلاثة أشهر', 'mandatory' => true],
                    ['name' => 'Owner/Partners Approval or Court Ruling', 'name_ar' => 'موافقة المالك أو الشركاء أو حكم قضائي', 'mandatory' => true],
                    ['name' => 'Occupancy Permit Copy', 'name_ar' => 'رخصة الإشغال أو نسخة طبق الأصل عنها', 'mandatory' => true],
                    ['name' => 'Legal Valuation Certificate (price per sqm)', 'name_ar' => 'إفادة تخمين قانونية تحدد السعر البيعي للمتر المربع', 'mandatory' => false],
                ],
            ],
            [
                'name' => 'Lease Contract Registration',
                'name_ar' => 'تسجيل عقد إيجار',
                'description' => 'Official registration of a residential or commercial lease agreement with the municipality (Form T-12).',
                'description_ar' => 'تسجيل رسمي لعقد إيجار سكني أو تجاري لدى البلدية (نموذج ط ١٢).',
                'price' => 20.00,
                'processing_days' => 5,
                'documents' => [
                    ['name' => 'Lease Contract and Annex', 'name_ar' => 'عقد الإيجار وملحقه', 'mandatory' => true],
                    ['name' => 'Two Additional Copies of the Lease Contract and Annex', 'name_ar' => 'نسختان عن عقد الإيجار وملحقه', 'mandatory' => true],
                    ['name' => 'Real Estate Certificate (max 3 months old, if new contract)', 'name_ar' => 'إفادة عقارية لا يعود تاريخها لأكثر من ثلاثة أشهر (في حال كان العقد جديداً)', 'mandatory' => true],
                    ['name' => 'ID Copy (only if registering the property for the first time)', 'name_ar' => 'صورة عن الهوية (في حال كان العقار يسجل للمرة الأولى)', 'mandatory' => false],
                ],
            ],
            [
                'name' => 'Municipal Clearance Certificate',
                'name_ar' => 'براءة ذمة بلدية',
                'description' => 'Certificate confirming no outstanding dues are owed to the municipality for a given property (Form T-22). Often required before a property sale.',
                'description_ar' => 'شهادة تؤكد عدم وجود مستحقات معلقة للبلدية عن عقار معين (نموذج ط ٢٢)، تُطلب غالباً قبل بيع عقار.',
                'price' => 15.00,
                'processing_days' => 5,
                'documents' => [
                    ['name' => 'Title Deed or Sale Document Copy', 'name_ar' => 'صورة عن سند الملكية أو مستند بيع', 'mandatory' => true],
                    ['name' => 'Comprehensive Real Estate Certificate (max 3 months old)', 'name_ar' => 'إفادة عقارية شاملة لا يعود تاريخها لأكثر من ثلاثة أشهر', 'mandatory' => true],
                    ['name' => 'Subdivision Map', 'name_ar' => 'خريطة إفراز', 'mandatory' => true],
                    ['name' => 'Official Building Legality Document', 'name_ar' => 'مستند رسمي عن قانونية البناء', 'mandatory' => true],
                    ['name' => 'Planning Certificate', 'name_ar' => 'إفادة تخطيط', 'mandatory' => false],
                ],
            ],
            [
                'name' => 'Complaint or Inquiry',
                'name_ar' => 'شكوى أو مراجعة',
                'description' => 'Submit a formal complaint or request a review of a municipal transaction (Form T-14). No supporting documents required.',
                'description_ar' => 'تقديم شكوى رسمية أو طلب مراجعة معاملة بلدية (نموذج ط ١٤). لا تتطلب مستندات داعمة.',
                'price' => 0.00,
                'processing_days' => 7,
                'documents' => [],
            ],
            [
                'name' => 'Advertisement Permit (Permanent or Temporary)',
                'name_ar' => 'ترخيص للإعلان الدائم والمؤقت',
                'description' => 'Permit required to install a permanent or temporary advertising billboard or sign (Decree 96/8861, Article 7).',
                'description_ar' => 'رخصة مطلوبة لتركيب لوحة إعلانية دائمة أو مؤقتة (مرسوم رقم ٩٦/٨٨٦١، المادة ٧).',
                'price' => 80.00,
                'processing_days' => 14,
                'documents' => [
                    ['name' => 'Signed Permit Request', 'name_ar' => 'طلب موقع من صاحب العلاقة', 'mandatory' => true],
                    ['name' => 'Real Estate Certificate for the Property (max 3 months old)', 'name_ar' => 'إفادة عقارية للعقار الذي سيوضع الإعلان فيه', 'mandatory' => true],
                    ['name' => 'Easement & Planning Certificate (max 3 months old)', 'name_ar' => 'إفادة ارتفاق وتخطيط', 'mandatory' => true],
                    ['name' => 'Third-Party Insurance Policy for the Sign', 'name_ar' => 'بوليصة تأمين للوحة ضد الغير', 'mandatory' => true],
                    ['name' => 'Power Network Non-Encroachment Document', 'name_ar' => 'مستند يثبت عدم إضاءة الإعلان بواسطة التعدي على شبكات الطاقة', 'mandatory' => true],
                    ['name' => 'Notarized Owner Contract or Public Property Permit', 'name_ar' => 'عقد مع صاحب العقار مسجل لدى الكاتب العدل أو ترخيص من الإدارة المختصة', 'mandatory' => true],
                    ['name' => 'Site & Billboard Maps (1/100 & 1/20 scale, 3 copies)', 'name_ar' => 'ثلاث نسخ عن خريطة الموقع وخريطة اللوحة الإعلانية', 'mandatory' => true],
                    ['name' => 'Building Facade Photo (1/50 scale, 3 copies)', 'name_ar' => 'ثلاث نسخ عن مصور واجهة البناء', 'mandatory' => true],
                    ['name' => 'Site Photograph', 'name_ar' => 'صورة فوتوغرافية للموقع', 'mandatory' => true],
                ],
            ],
            [
                'name' => 'Renovation, Retaining Wall, Land Leveling & Demolition Permit',
                'name_ar' => 'تصريح بالترميم أو بناء جدران دعم أو أعمال تسويات الأرض أو أعمال الهدم',
                'description' => 'Permit for minor renovation work, support/retaining walls, land leveling, or demolition (Form T-17).',
                'description_ar' => 'تصريح لأعمال ترميم بسيطة أو بناء جدران دعم أو تسوية أرض أو أعمال هدم (نموذج ط ١٧).',
                'price' => 60.00,
                'processing_days' => 14,
                'documents' => [
                    ['name' => 'Written Permit Request with Map/Photo', 'name_ar' => 'طلب تصريح خطي مع خريطة أو مصور عند الاقتضاء', 'mandatory' => true],
                    ['name' => 'Survey Certificate (if no final survey map exists)', 'name_ar' => 'إفادة مساحة (في حال عدم وجود خريطة مساحة نهائية)', 'mandatory' => false],
                    ['name' => 'Easement & Planning Certificate', 'name_ar' => 'إفادة ارتفاق وتخطيط', 'mandatory' => true],
                    ['name' => 'Comprehensive Real Estate Certificate (max 3 months old)', 'name_ar' => 'إفادة عقارية شاملة لا يعود تاريخها لأكثر من ثلاثة أشهر', 'mandatory' => true],
                    ['name' => "Engineer's Pledge (for structural work)", 'name_ar' => 'تعهد مهندس عند أعمال إنشائية', 'mandatory' => true],
                    ['name' => 'Owner Written Approval or Court Ruling (+ Lease Copy if Tenant)', 'name_ar' => 'موافقة خطية من المالك أو حكم قضائي (وصورة عقد الإيجار إذا كان المستدعي مستأجراً)', 'mandatory' => true],
                    ['name' => 'Before/After Land Survey Maps', 'name_ar' => 'خرائط الأرض الطبيعية قبل المباشرة بالعمل وبعد انتهائه', 'mandatory' => true],
                ],
            ],
        ];
    }
}
