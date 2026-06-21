<?php
namespace Database\Seeders;

use App\Models\Office;
use App\Models\Revenue;
use App\Models\User;
use Illuminate\Database\Seeder;

class DashboardSeeder extends Seeder
{
    public function run(): void
    {
        // ── Create 30 sample citizens ──────────────────────────────────────
        // We removed the broken 'municipality' text column assignment here!
        User::factory(30)->create([
            'role' => 'citizen',
        ]);

        // ── Create sample offices & revenues ───────────────────────────────
        /*
        ========================================================================
        COMMENTED OUT: Person D's dummy data relies on a text 'municipality' column,
        but Person A built an official relational 'municipalities' table!
        We are disabling this to prevent the Error 1054 database crash.
        ========================================================================

        $municipalities = ['Manila', 'Quezon City', 'Makati', 'Pasig', 'Taguig', 'Caloocan'];

        foreach ($municipalities as $city) {
            $office = Office::create([
                'name'         => "{$city} City Hall",
                'municipality' => $city,
                'address'      => "City Hall Compound, {$city}",
                'email'        => strtolower(str_replace(' ', '', $city)) . '@gov.ph',
                'is_active'    => true,
            ]);

            for ($i = 0; $i < rand(3, 8); $i++) {
                Revenue::create([
                    'office_id'        => $office->id,
                    'amount'           => rand(5000, 150000) + (rand(0, 99) / 100),
                    'description'      => 'Service fee collection',
                    'transaction_date' => now()->subDays(rand(0, 90)),
                ]);
            }
        }
        */

        $this->command->info('Dashboard seed data adjusted for schema and created successfully.');
    }
}
