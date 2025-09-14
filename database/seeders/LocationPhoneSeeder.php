<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Location;

class LocationPhoneSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Sample phone data for existing locations
        $locationPhones = [
            'Main Store' => '021-7654321',
            'Warehouse' => '021-7654322', 
            'Branch A' => '021-7654323',
            'Branch B' => '021-7654324',
            'Istana Batu Alam Store' => '021-7654325',
            'Outlet 1' => '0812-3456-7890',
            'Outlet 2' => '0813-3456-7891',
        ];

        foreach ($locationPhones as $name => $phone) {
            Location::where('name', 'like', "%{$name}%")
                ->orWhere('name', $name)
                ->update(['phone' => $phone]);
        }

        // Update any locations without phone numbers
        Location::whereNull('phone')->update(['phone' => '021-1234567']);
        
        echo "Location phone numbers updated successfully!\n";
    }
}