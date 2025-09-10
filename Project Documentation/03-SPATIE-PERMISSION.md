# 🔐 Spatie Permission System - Complete Guide

**Package**: Spatie Laravel Permission v6.21  
**Status**: ✅ Fully Configured and Working  
**Last Updated**: September 7, 2025

### ✅ Package Installation
- **Package**: `spatie/laravel-permission` versi `^6.21`
- **Status**: ✅ Terinstall dan terkonfigurasi dengan benar
- **Discovery**: ✅ Package terdaftar dalam `php artisan package:discover`

### ✅ Database Tables
Tabel yang dibuat oleh migrasi `2025_09_05_210616_create_permission_tables.php`:
- `permissions` - Menyimpan daftar permission
- `roles` - Menyimpan daftar role
- `model_has_permissions` - Pivot table user-permission langsung
- `model_has_roles` - Pivot table user-role
- `role_has_permissions` - Pivot table role-permission

### ✅ Model Integration
**User Model** (`app/Models/User.php`):
```php
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable {
    use HasRoles; // ✅ Trait sudah ada dan benar
}
```

## Struktur Permission dan Roles

### Data yang Sudah di-Seed

**Total Data (berdasarkan database check):**
- **Roles**: 7 roles
- **Permissions**: 50 permissions
- **Users with roles**: 3 users

### Daftar Permissions Lengkap

#### Master Data
```
products.read, products.create, products.update, products.delete
categories.read, categories.create, categories.update, categories.delete
suppliers.read, suppliers.create, suppliers.update, suppliers.delete
customers.read, customers.create, customers.update, customers.delete
```

#### Sales Operations
```
sales.read, sales.create, sales.update, sales.delete
sales.post, sales.void, sales.use_remote_stock
```

#### Purchase Operations
```
purchases.read, purchases.create, purchases.update, purchases.delete
purchases.receive, purchases.post, purchases.void
```

#### Stock Management
```
stock_mutations.request, stock_mutations.confirm, stock_mutations.reject
stocks.read, stocks.adjust
```

#### Delivery Management
```
deliveries.read, deliveries.assign, deliveries.update_status
```

#### Financial & Reports
```
expenses.read, expenses.create, expenses.update, expenses.delete
reports.sales, reports.stock, reports.purchase, reports.finance
```

#### Administration
```
admin.users, admin.roles, admin.permissions, admin.locations
```

### Role Hierarchy dan Permissions

#### 1. Super-Admin & Admin
- **Access**: Full access ke semua 50 permissions
- **Usage**: Administrator system

#### 2. Manager
- **Permissions**: `reports.*`, `sales.*`, `purchases.*`, `stocks.read`, `stock_mutations.*`
- **Usage**: Manajemen operasional

#### 3. Cashier
- **Permissions**: `sales.read`, `sales.create`, `sales.update`, `sales.post`, `sales.use_remote_stock`
- **Usage**: Kasir/front office

#### 4. Warehouse & Kepala-Gudang
- **Permissions**: `stocks.read`, `stocks.adjust`, `purchases.read`, `purchases.receive`, `stock_mutations.*`
- **Usage**: Manajemen gudang

#### 5. Driver
- **Permissions**: `deliveries.read`, `deliveries.update_status`
- **Usage**: Driver delivery

## Middleware Configuration

### ✅ Correct Registration (bootstrap/app.php)
```php
$middleware->alias([
    'active.location' => \App\Http\Middleware\ActiveLocation::class,
    // Spatie Permission middleware (original)
    'permission' => \Spatie\Permission\Middleware\PermissionMiddleware::class,
    'role' => \Spatie\Permission\Middleware\RoleMiddleware::class,
    'role_or_permission' => \Spatie\Permission\Middleware\RoleOrPermissionMiddleware::class,
]);
```

### ❌ Previous Wrong Configuration (FIXED)
```php
// JANGAN GUNAKAN INI LAGI:
'permission' => \App\Http\Middleware\SimplePermissionMiddleware::class,
```

## Route Protection Implementation

### Contoh Route dengan Permission
```php
// Master Data
Route::get('/products', [ProductsController::class, 'index'])
    ->middleware('permission:products.read')->name('products.index');

Route::get('/categories', [CategoryController::class, 'index'])
    ->middleware('permission:categories.read')->name('categories.index');

// Sales
Route::get('/sales', [SalesController::class, 'index'])
    ->middleware('permission:sales.read')->name('sales.index');

Route::post('/sales', [SalesController::class, 'store'])
    ->middleware('permission:sales.create')->name('sales.store');
```

## Usage dalam Code

### 1. Controller Permission Check
```php
// Di SalesController
if (!auth()->user()->can('sales.use_remote_stock')) {
    throw new AccessDeniedHttpException('No permission: sales.use_remote_stock');
}
```

### 2. Blade Template
```php
@can('products.create')
    <button>Create Product</button>
@endcan

@role('admin')
    <div>Admin only content</div>
@endrole
```

### 3. Programmatic Check
```php
// Check permission
if (auth()->user()->can('sales.read')) {
    // Allow access
}

// Check role
if (auth()->user()->hasRole('admin')) {
    // Admin access
}

// Check multiple permissions
if (auth()->user()->hasAnyPermission(['sales.read', 'sales.create'])) {
    // Has any of these permissions
}
```

## Default Users dan Roles

### Admin User (dari AdminSeeder)
```php
Email: admin@example.com
Password: password
Role: super-admin
Locations: Main Store (default)
```

## Commands untuk Debugging

### Check Permission Status
```bash
# Lihat permission matrix
php artisan permission:show

# Check cache
php artisan permission:cache-reset

# Assign permission to user
php artisan tinker
>>> $user = User::find(1);
>>> $user->givePermissionTo('products.read');
>>> $user->assignRole('admin');
```

## Seeder Information

### PermissionsSeeder.php
- **Location**: `database/seeders/PermissionsSeeder.php`
- **Function**: Membuat 50 permissions dan 7 roles dengan mapping yang benar
- **Run order**: Pertama dalam DatabaseSeeder

### AdminSeeder.php
- **Location**: `database/seeders/AdminSeeder.php`
- **Function**: Membuat admin user dengan role `super-admin`
- **Run order**: Kedua setelah PermissionsSeeder

## Troubleshooting Guide

### Error 500 pada Route dengan Permission
**Cause**: Middleware registration salah
**Solution**: Pastikan menggunakan middleware Spatie asli, bukan custom

### Permission Denied (403)
**Cause**: User tidak memiliki permission yang diperlukan
**Debug**:
```php
// Check user permissions
dd(auth()->user()->getAllPermissions());

// Check user roles
dd(auth()->user()->getRoleNames());
```

### Cache Issues
**Solution**:
```bash
php artisan cache:clear
php artisan route:clear
php artisan config:clear
php artisan permission:cache-reset
```

## API Endpoints (Bypass Permission)

### Live Search API
**Route**: `/api/products/search`
**Purpose**: AJAX live search tanpa middleware permission
**Reason**: Untuk menghindari kompleksitas permission pada search

```php
Route::get('/api/products/search', function (Request $request) {
    // No middleware - direct database access
    // Used for live search functionality
});
```

## Important Notes

### 1. Developer Mode
- File: `app/Http/Middleware/DeveloperMode.php`
- Purpose: Auto-login pada development environment
- Status: ✅ Aktif dan registered sebagai global middleware

### 2. Permission Caching
- Spatie menggunakan cache untuk performance
- Cache key: `spatie.permission.cache`
- Expiration: 24 hours
- Auto-clear ketika permission/role updated

### 3. Team Feature
- Status: ❌ Disabled (`'teams' => false`)
- Jika butuh multi-tenant, bisa diaktifkan

## Testing Commands

```bash
# Test permission system
php artisan tinker --execute="
\$user = \App\Models\User::first();
echo 'User: ' . \$user->name;
echo '\nRoles: ' . \$user->getRoleNames();
echo '\nCan read products: ' . (\$user->can('products.read') ? 'YES' : 'NO');
"

# Test route access
curl -H "Accept: application/json" http://127.0.0.1:8000/products
```

## Maintenance

### Adding New Permission
1. Update `PermissionsSeeder.php`
2. Assign ke appropriate roles
3. Run seeder: `php artisan db:seed --class=PermissionsSeeder`

### Adding New Role
1. Update `PermissionsSeeder.php` roles array
2. Define permission mapping
3. Run seeder

---


**Last Updated**: September 10, 2025
**Status**: ✅ System working correctly, CRUD role & permission UI, modal, AJAX submit implemented
**Summary**:
- CRUD role & permission di UI sudah selesai (controller, view, route)
- Sidebar navigation permission-based sudah aktif
- UI permission grouping & collapsible sudah diterapkan
- Modal create form & AJAX submit sudah terintegrasi
- Konsistensi desain card/tabel/tombol di roles, permissions, users
- Error 500 pada /admin/roles sudah diperbaiki
- Troubleshooting: Pastikan controller selalu mengirim data yang dibutuhkan ke view/modal
- Next session: Lanjutkan pengembangan dan validasi permission system sesuai dokumentasi
