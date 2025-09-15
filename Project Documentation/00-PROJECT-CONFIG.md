# 2025-09-10
- Toast notification: partial _flash_notify, Blade stack, Alpine.js
- UI konsisten: purchases, suppliers, customers
- Diagnostik: window.notify error, troubleshooting, best practice
# 🚀 IBA POS - Project Configuration

## 📍 **CRITICAL PROJECT PATHS** 
> AI: Use these exact paths - NO path detection needed!

### Main Project Directory
```
PROJECT_ROOT = "c:\xampp\htdocs\IBAPOS"
```

### Key Folders
```
APP_PATH = "c:\xampp\htdocs\IBAPOS\app"
VIEWS_PATH = "c:\xampp\htdocs\IBAPOS\resources\views"
ROUTES_PATH = "c:\xampp\htdocs\IBAPOS\routes"
CONFIG_PATH = "c:\xampp\htdocs\IBAPOS\config"
DATABASE_PATH = "c:\xampp\htdocs\IBAPOS\database"
DOCS_PATH = "c:\xampp\htdocs\Catatan Project"
```

## � **RECENT CRITICAL FIXES** (September 8, 2025)

### ✅ System Status: FULLY OPERATIONAL
- **Products Page**: 500 error resolved ✅
- **Live Search**: API endpoints working ✅
- **Developer Mode**: Auto-login functional ✅
- **Permission System**: Bypass active ✅

### Fixed Issues
1. **DeveloperPermissionMiddleware**: Method signature compatibility fixed
2. **Routes Import**: Added `use Illuminate\Http\Request;` to routes/web.php
3. **Migration Conflicts**: Duplicate image_path column resolved
4. **API Authentication**: Enhanced auth checks in ProductController

### ⚠️ CRITICAL - DO NOT CHANGE
```php
// ✅ WORKING - Keep this middleware registration:
'permission' => \App\Http\Middleware\DeveloperPermissionMiddleware::class,

// ✅ WORKING - Keep this method signature:
public function handle($request, Closure $next, $permission, $guard = null)
```

## �💻 **SHELL ENVIRONMENT**
> AI: Use PowerShell commands - Windows environment!

### Shell Type
```
SHELL = "powershell.exe" (Windows PowerShell v5.1)
```

### Command Syntax Rules
 **Path navigation**: Use quotes for paths with spaces: `cd "c:\xampp\htdocs\IBAPOS"`
- **Command chaining**: Use semicolon `;` NOT `&&`: `composer install; php artisan serve`
- **File operations**: Use PowerShell syntax: `Get-Content file.txt`, `Remove-Item file.txt`

### Common Commands Template
cd "c:\xampp\htdocs\IBAPOS"
# Navigate to project
cd "c:\xampp\htdocs\IBAPOS"

# Laravel operations
php artisan serve                    # Start server (default port 8000)
php artisan cache:clear              # Clear cache
php artisan route:clear              # Clear routes
php artisan config:clear             # Clear config

# Multi-command execution
php artisan cache:clear; php artisan route:clear; php artisan config:clear

# Check files
Get-Content storage\logs\laravel.log -Tail 10

# Composer operations
composer install
composer dump-autoload
```

## 🛠️ **DEVELOPMENT ENVIRONMENT**

### Technology Stack
- **Framework**: Laravel 12.0 (PHP 8.2+)
- **Frontend**: Alpine.js + Tailwind CSS v4
- **Database**: MySQL (via XAMPP)
- **Permission**: Spatie Permission v6.21
- **Server**: Built-in Laravel dev server

### Default Access
```
URL: http://127.0.0.1:8000
Admin: admin@example.com / password
Role: super-admin (all permissions)
```

### Development Features
- **Developer Mode**: Auto-login enabled (DeveloperMode middleware)
- **Environment**: Local (`APP_ENV=local`)
- **Debug**: Enabled (`APP_DEBUG=true`)
- **Cache**: Development mode (no optimization)

## 🏗️ **PROJECT STRUCTURE REFERENCE**

```
c:\xampp\htdocs\
├── IBAPOS/                          # Main Laravel application
│   ├── app/
│   │   ├── Http/Controllers/        # Controllers
│   │   ├── Http/Middleware/         # Custom middleware  
│   │   ├── Models/                  # Eloquent models
│   │   └── Services/                # Business logic
│   ├── bootstrap/
│   │   └── app.php                  # ⚠️ Middleware config (DO NOT CHANGE)
│   ├── config/                      # Configuration files
│   ├── database/
│   │   ├── migrations/              # Database migrations
│   │   └── seeders/                 # Data seeders
│   ├── resources/
│   │   └── views/                   # Blade templates
│   ├── routes/
│   │   └── web.php                  # Route definitions
│   └── storage/
│       └── logs/                    # Application logs
└── Catatan Project/                 # 📚 Project documentation
    ├── 00-PROJECT-CONFIG.md         # This file - project paths & commands
    ├── 01-SYSTEM-STATUS.md          # Current system status & working features
    ├── 02-DEVELOPER-MODE.md         # Developer mode configuration & security
    ├── 03-SPATIE-PERMISSION.md     # Permission system documentation
    ├── 04-ISSUE-RESOLUTION.md      # Solved issues & debugging guide
    └── README.md                    # Main project overview
```

## 🎯 **QUICK START FOR AI**

### Essential First Commands
```powershell
# 1. Navigate to project
cd "c:\xampp\htdocs\IBAPOS"

# 2. Start server (if not running)
php artisan serve

# 3. Clear all caches (after changes)
php artisan cache:clear; php artisan route:clear; php artisan config:clear

# 4. Check system status
php artisan permission:show
```

### File Reading Priority
1. **System Status**: `01-SYSTEM-STATUS.md` - What's working now
2. **Known Issues**: `04-ISSUE-RESOLUTION.md` - What NOT to break
3. **Permissions**: `03-SPATIE-PERMISSION.md` - How auth works
4. **Developer Mode**: `02-DEVELOPER-MODE.md` - Security implications

---

**⚡ AI Note**: This file contains ALL path and command information needed. Reference this file first before any operation to avoid path detection overhead.
