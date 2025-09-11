<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class PermissionsSeeder extends Seeder
{
    public function run(): void
    {
        $permissions = [
            // masters
            'products.read','products.create','products.update','products.delete',
            'categories.read','categories.create','categories.update','categories.delete',
            'expense_categories.read','expense_categories.create','expense_categories.update','expense_categories.delete',
            'suppliers.read','suppliers.create','suppliers.update','suppliers.delete',
            'customers.read','customers.create','customers.update','customers.delete',
            // sales
            'sales.read','sales.create','sales.update','sales.delete','sales.post','sales.void','sales.use_remote_stock',
            // purchases
            'purchases.read','purchases.create','purchases.update','purchases.delete','purchases.receive','purchases.post','purchases.void',
            // stocks
            'stock_mutations.request','stock_mutations.confirm','stock_mutations.reject','stocks.read','stocks.adjust',
            // deliveries
            'deliveries.read','deliveries.assign','deliveries.update_status',
            // expenses & reports
            'expenses.read','expenses.create','expenses.update','expenses.delete',
            'reports.sales','reports.stock','reports.purchase','reports.finance',
            // admin
            'admin.users','admin.roles','admin.permissions','admin.locations',
            // kasbons
            'kasbons.read','kasbons.create','kasbons.update','kasbons.delete',
        ];

        foreach ($permissions as $name) {
            Permission::firstOrCreate(['name' => $name]);
        }

        $roles = [
            'super-admin' => $permissions,
            'admin' => $permissions,
            'manager' => ['reports.*','sales.*','purchases.*','stocks.read','stock_mutations.*'],
            'cashier' => ['sales.read','sales.create','sales.update','sales.post','sales.use_remote_stock'],
            'warehouse' => ['stocks.read','stocks.adjust','stock_mutations.*','purchases.read','purchases.receive'],
            'driver' => ['deliveries.read','deliveries.update_status'],
            'kepala-gudang' => ['stocks.read','stocks.adjust','purchases.read','purchases.receive','stock_mutations.*'],
        ];

        foreach ($roles as $roleName => $perms) {
            $role = Role::firstOrCreate(['name' => $roleName]);
            if ($perms === $permissions) {
                $role->syncPermissions($permissions);
            } else {
                $expanded = [];
                foreach ($perms as $p) {
                    if (str_contains($p, '.*')) {
                        $prefix = rtrim($p, '.*');
                        $expanded = array_merge($expanded, array_filter($permissions, fn($x) => str_starts_with($x, $prefix)));
                    } else {
                        $expanded[] = $p;
                    }
                }
                $role->syncPermissions(array_unique($expanded));
            }
        }
    }
}
