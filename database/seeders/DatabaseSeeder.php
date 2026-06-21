<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        User::firstOrCreate(['email' => 'admin@eservices.com'], [
            'first_name' => 'Admin',
            'last_name'  => 'User',
            'password'   => Hash::make('password'),
            'role'       => 'admin',
            'status'     => 'active',
        ]);

        User::firstOrCreate(['email' => 'office@eservices.com'], [
            'first_name' => 'Office',
            'last_name'  => 'Staff',
            'password'   => Hash::make('password'),
            'role'       => 'office',
            'status'     => 'active',
        ]);

        User::firstOrCreate(['email' => 'citizen@eservices.com'], [
            'first_name' => 'Juan',
            'last_name'  => 'dela Cruz',
            'password'   => Hash::make('password'),
            'role'       => 'citizen',
            'status'     => 'active',
        ]);

        $this->call([
            MunicipalityOfficeServiceSeeder::class,
            DashboardSeeder::class,
            RealMuni