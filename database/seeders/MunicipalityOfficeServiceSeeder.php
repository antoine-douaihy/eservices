<?php

namespace Database\Seeders;

use App\Models\Municipality;
use App\Models\Office;
use App\Models\RequiredDocument;
use App\Models\Service;
use Illuminate\Database\Seeder;

class MunicipalityOfficeServiceSeeder extends Seeder
{
    public function run(): void
    {
        // ── Municipalities ──────────────────────────────────────────
        $municipalities = [
            ['name' => 'Beirut',       'region' => 'Beirut Governorate'],
            ['name' => 'Tripoli',      'region' => 'North Governorate'],
            ['name' => 'Sidon',        'region' => 'South Governorate'],
            ['name' => 'Tyre',         'region' => 'South Governorate'],
            ['name' => 'Jounieh',      'region' => 'Mount Lebanon'],
            ['name' => 'Baalbek',      'region' => 'Baalbek-Hermel'],
            ['name' => 'Zahle',        'region' => 'Beqaa Governorate'],
        ];

        $createdMunicipalities = [];
        foreach ($municipalities as $m) {
            $createdMunicipalities[$m['name']] = Municipality::firstOrCreate(
                ['name' => $m['name']],
                ['region' => $m['region']]
            );
        }

        // ── Offices ─────────────────────────────────────────────────
        $officeData = [
            [
                'municipality' => 'Beirut',
                'name'         => 'Beirut City Hall – Civil Registry',
                'code'         => 'BEY-001',
                'address'      => 'Riad El Solh Square, Beirut',
                'city'         => 'Beirut',
                'phone'        => '+961 1 980 380',
                'email'        => 'civil@beirut.gov.lb',
                'latitude'     => 33.8938,
                'longitude'    => 35.5018,
            ],
            [
                'municipality' => 'Beirut',
                'name'         => 'Beirut Vehicle Registration Office',
                'code'         => 'BEY-002',
                'address'      => 'Museum Square, Beirut',
                'city'         => 'Beirut',
                'phone'        => '+961 1 422 000',
                'email'        => 'vehicles@beirut.gov.lb',
                'latitude'     => 33.8869,
                'longitude'    => 35.5131,
            ],
            [
                'municipality' => 'Tripoli',
                'name'         => 'Tripoli Municipal Services',
                'code'         => 'TRP-001',
                'address'      => 'Al-Tall Square, Tripoli',
                'city'         => 'Tripoli',
                'phone'        => '+961 6 628 000',
                'email'        => 'services@tripoli.gov.lb',
                'latitude'     => 34.4367,
                'longitude'    => 35.8497,
            ],
            [
                'municipality' => 'Sidon',
                'name'         => 'Sidon City Hall',
                'code'         => 'SID-001',
                'address'      => 'Municipality Building, Sidon',
                'city'         => 'Sidon',
                'phone'        => '+961 7 720 100',
                'email'        => 'hall@sidon.gov.lb',
                'latitude'     => 33.5632,
                'longitude'    => 35.3712,
            ],
            [
                'municipality' => 'Jounieh',
                'name'         => 'Jounieh Civil Affairs Office',
                'code'         => 'JOU-001',
                'address'      => 'Main Road, Jounieh',
                'city'         => 'Jounieh',
                'phone'        => '+961 9 910 200',
                'email'        => 'civil@jounieh.gov.lb',
                'latitude'     => 33.9806,
                'longitude'    => 35.6178,
            ],
            [
                'municipality' => 'Zahle',
                'name'         => 'Zahle Government Services Center',
                'code'         => 'ZAH-001',
                'address'      => 'Zahle Central District',
                'city'         => 'Zahle',
                'phone'        => '+961 8 800 100',
                'email'        => 'services@zahle.gov.lb',
                'latitude'     => 33.8469,
                'longitude'    => 35.9022,
            ],
        ];

        $createdOffices = [];
        foreach ($officeData as $od) {
            $municipality = $createdMunicipalities[$od['municipality']];
            $createdOffices[$od['code']] = Office::firstOrCreate(
                ['code' => $od['code']],
                [
                    'name'            => $od['name'],
                    'address'         => $od['address'],
                    'city'            => $od['city'],
                    'phone'           => $od['phone'],
                    'email'           => $od['email'],
                    'municipality_id' => $municipality->id,
                    'latitude'        => $od['latitude'],
                    'longitude'       => $od['longitude'],
                    'is_active'       => true,
                ]
            );
        }

        // ── Services ─────────────────────────────────────────────────
        $serviceDefinitions = [
            [
                'office_code'     => 'BEY-001',
                'name'            => 'Birth Certificate Copy',
                'description'     => 'Official certified copy of birth certificate for legal and administrative use.',
                'price'           => 15.00,
                'currency'        => 'USD',
                'processing_days' => 3,
                'documents'       => [
                    ['name' => 'National ID Card',          'is_mandatory' => true,  'sort_order' => 1],
                    ['name' => 'Proof of Relationship',     'is_mandatory' => true,  'sort_order' => 2],
                    ['name' => 'Previous Certificate Copy', 'is_mandatory' => false, 'sort_order' => 3],
                ],
            ],
            [
                'office_code'     => 'BEY-001',
                'name'            => 'Marriage Certificate',
                'description'     => 'Official marriage certificate for civil and religious unions.',
                'price'           => 25.00,
                'currency'        => 'USD',
                'processing_days' => 5,
                'documents'       => [
                    ['name' => 'National ID Card (Both Parties)', 'is_mandatory' => true,  'sort_order' => 1],
                    ['name' => 'Religious Certificate',           'is_mandatory' => true,  'sort_order' => 2],
                    ['name' => 'Witness Documents',               'is_mandatory' => false, 'sort_order' => 3],
                ],
            ],
            [
                'office_code'     => 'BEY-001',
                'name'            => 'Death Certificate',
                'description'     => 'Official death certificate for estate and legal proceedings.',
                'price'           => 10.00,
                'currency'        => 'USD',
                'processing_days' => 2,
                'documents'       => [
                    ['name' => 'Hospital Death Report',   'is_mandatory' => true,  'sort_order' => 1],
                    ['name' => 'National ID of Deceased', 'is_mandatory' => true,  'sort_order' => 2],
                    ['name' => 'Applicant National ID',   'is_mandatory' => true,  'sort_order' => 3],
                ],
            ],
            [
                'office_code'     => 'BEY-002',
                'name'            => 'Vehicle Registration',
                'description'     => 'Register a new or imported vehicle with the Lebanese transport authority.',
                'price'           => 75.00,
                'currency'        => 'USD',
                'processing_days' => 7,
                'documents'       => [
                    ['name' => 'Vehicle Purchase Invoice',  'is_mandatory' => true,  'sort_order' => 1],
                    ['name' => 'Owner National ID',         'is_mandatory' => true,  'sort_order' => 2],
                    ['name' => 'Technical Inspection Form', 'is_mandatory' => true,  'sort_order' => 3],
                    ['name' => 'Insurance Certificate',     'is_mandatory' => true,  'sort_order' => 4],
                ],
            ],
            [
                'office_code'     => 'BEY-002',
                'name'            => 'Driver License Renewal',
                'description'     => 'Renew an expired or expiring Lebanese driver\'s license.',
                'price'           => 40.00,
                'currency'        => 'USD',
                'processing_days' => 5,
                'documents'       => [
                    ['name' => 'Current Driver License',  'is_mandatory' => true,  'sort_order' => 1],
                    ['name' => 'National ID Card',        'is_mandatory' => true,  'sort_order' => 2],
                    ['name' => 'Medical Fitness Report',  'is_mandatory' => false, 'sort_order' => 3],
                ],
            ],
            [
                'office_code'     => 'TRP-001',
                'name'            => 'Residency Certificate',
                'description'     => 'Official certificate confirming your place of residence within the municipality.',
                'price'           => 0.00,
                'currency'        => 'USD',
                'processing_days' => 1,
                'documents'       => [
                    ['name' => 'National ID Card',  'is_mandatory' => true,  'sort_order' => 1],
                    ['name' => 'Utility Bill',      'is_mandatory' => true,  'sort_order' => 2],
                ],
            ],
            [
                'office_code'     => 'TRP-001',
                'name'            => 'Birth Certificate Copy',
                'description'     => 'Official certified copy of birth certificate for legal and administrative use.',
                'price'           => 15.00,
                'currency'        => 'USD',
                'processing_days' => 3,
                'documents'       => [
                    ['name' => 'National ID Card',      'is_mandatory' => true,  'sort_order' => 1],
                    ['name' => 'Proof of Relationship', 'is_mandatory' => true,  'sort_order' => 2],
                ],
            ],
            [
                'office_code'     => 'SID-001',
                'name'            => 'Business License',
                'description'     => 'Obtain a commercial license to operate a business within the municipality.',
                'price'           => 120.00,
                'currency'        => 'USD',
                'processing_days' => 14,
                'documents'       => [
                    ['name' => 'National ID Card',         'is_mandatory' => true,  'sort_order' => 1],
                    ['name' => 'Business Registration',    'is_mandatory' => true,  'sort_order' => 2],
                    ['name' => 'Property Lease Agreement', 'is_mandatory' => true,  'sort_order' => 3],
                    ['name' => 'Tax Clearance Letter',     'is_mandatory' => false, 'sort_order' => 4],
                ],
            ],
            [
                'office_code'     => 'JOU-001',
                'name'            => 'Nationality Certificate',
                'description'     => 'Official document confirming Lebanese nationality status.',
                'price'           => 20.00,
                'currency'        => 'USD',
                'processing_days' => 5,
                'documents'       => [
                    ['name' => 'National ID Card',        'is_mandatory' => true,  'sort_order' => 1],
                    ['name' => 'Family Registry Extract', 'is_mandatory' => true,  'sort_order' => 2],
                ],
            ],
            [
                'office_code'     => 'ZAH-001',
                'name'            => 'Land Registration',
                'description'     => 'Register or transfer property ownership in official land records.',
                'price'           => 200.00,
                'currency'        => 'USD',
                'processing_days' => 21,
                'documents'       => [
                    ['name' => 'Property Deed / Title',     'is_mandatory' => true,  'sort_order' => 1],
                    ['name' => 'Owner National ID',         'is_mandatory' => true,  'sort_order' => 2],
                    ['name' => 'Survey Map',                'is_mandatory' => true,  'sort_order' => 3],
                    ['name' => 'Tax Payment Receipt',       'is_mandatory' => true,  'sort_order' => 4],
                    ['name' => 'Notarised Sale Agreement',  'is_mandatory' => false, 'sort_order' => 5],
                ],
            ],
        ];

        foreach ($serviceDefinitions as $sd) {
            $office = $createdOffices[$sd['office_code']];

            $service = Service::firstOrCreate(
                ['slug' => \Illuminate\Support\Str::slug($sd['name']) . '-' . $sd['office_code']],
                [
                    'name'            => $sd['name'],
                    'description'     => $sd['description'],
                    'price'           => $sd['price'],
                    'currency'        => $sd['currency'],
                    'processing_days' => $sd['processing_days'],
                    'office_id'       => $office->id,
                    'is_active'       => true,
                ]
            );

            foreach ($sd['documents'] as $doc) {
                RequiredDocument::firstOrCreate(
                    ['service_id' => $service->id, 'name' => $doc['name']],
                    [
                        'is_mandatory' => $doc['is_mandatory'],
                        'sort_order'   => $doc['sort_order'],
                    ]
                );
            }
        }

        $this->command->info('Municipalities, offices, and services seeded successfully.');
    }
}
