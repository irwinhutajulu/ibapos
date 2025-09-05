<?php

namespace Database\Seeders;

use App\Models\Location;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UsersSeeder extends Seeder
{
    public function run(): void
    {
        // Ensure base locations exist (AdminSeeder creates some, but double-check)
        $main = Location::firstOrCreate(['name' => 'Main Store'], ['address' => '']);
        $warehouse = Location::firstOrCreate(['name' => 'Warehouse'], ['address' => '']);

        // 1) Admin: Irwin Hutajulu (role: admin)
        $irwin = User::firstOrCreate(
            ['email' => 'irwinhutajulu@gmail.com'],
            [
                'name' => 'Irwin Hutajulu',
                'password' => Hash::make('julu2985'),
                'email_verified_at' => now(),
            ]
        );
        $irwin->assignRole('admin'); // admin role has all permissions per PermissionsSeeder
        // Give admin access to all locations
        $irwin->locations()->syncWithoutDetaching(Location::pluck('id')->all());

        // 2) Cashier: Adi (role: cashier)
        $adi = User::firstOrCreate(
            ['email' => 'adi@nirmala.love'],
            [
                'name' => 'Adi',
                'password' => Hash::make('julu2985'),
                'email_verified_at' => now(),
            ]
        );
        $adi->assignRole('cashier');
        $adi->locations()->syncWithoutDetaching([$main->id]);
    }
}
