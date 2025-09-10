<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Location;

class LocationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $locations = [
            [
                'name' => 'Main Store',
                'address' => 'Jl. Raya No. 123, Jakarta Pusat, DKI Jakarta 10110',
            ],
            [
                'name' => 'Warehouse',
                'address' => 'Jl. Industri Raya No. 45, Bekasi, Jawa Barat 17530',
            ],
            [
                'name' => 'Outlet 2',
                'address' => 'Jl. Sudirman No. 789, Jakarta Selatan, DKI Jakarta 12190',
            ],
        ];

        foreach ($locations as $locationData) {
            Location::firstOrCreate(
                ['name' => $locationData['name']],
                $locationData
            );
        }
        
        // Assign all users to all locations for development
        $admin = \App\Models\User::where('email', 'admin@example.com')->first();
        if ($admin) {
            $locationIds = Location::pluck('id');
            $admin->locations()->syncWithoutDetaching($locationIds);
        }
    }
}
