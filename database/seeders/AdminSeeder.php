<?php

namespace Database\Seeders;

use App\Models\Location;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
    $loc = Location::firstOrCreate(['name' => 'Main Store'], ['address' => '']);
    Location::firstOrCreate(['name' => 'Warehouse'], ['address' => '']);
    Location::firstOrCreate(['name' => 'Outlet 2'], ['address' => '']);

        $user = User::firstOrCreate(
            ['email' => 'admin@example.com'],
            ['name' => 'Administrator', 'password' => Hash::make('password')]
        );

        $user->assignRole('super-admin');
        $user->locations()->syncWithoutDetaching([$loc->id]);
    }
}
