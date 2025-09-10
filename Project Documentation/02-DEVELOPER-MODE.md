# 🔓 Developer Mode - Security Configuration

**Current Status**: ✅ ACTIVE with Security Bypass Enabled

## 🎯 **DEVELOPER MODE OVERVIEW**

Developer Mode adalah system yang memudahkan development dengan auto-login dan security bypass untuk testing.

### Current Implementation Status
```
Environment: local ✅
DEVELOPER_MODE: true ✅
Auto-login: Active ✅
Security Bypass: ENABLED ✅ (Active)
Permission Checks: BYPASSED ✅
Middleware: DeveloperPermissionMiddleware ✅
```

## 🔧 **CONFIGURATION FILES**

### 1. Environment Configuration (.env)
```env
APP_ENV=local
DEVELOPER_MODE=true
APP_DEBUG=true
```

### 2. Config File (config/app.php)
```php
'developer_mode' => env('DEVELOPER_MODE', false),
```

### 3. Middleware Registration (bootstrap/app.php)
```php
// Developer mode is registered first (prepend)
$middleware->prepend(\App\Http\Middleware\DeveloperMode::class);

// Custom permission middleware with bypass capability
'permission' => \App\Http\Middleware\DeveloperPermissionMiddleware::class,
```

## 🔐 **SECURITY MODES**

### Mode 1: Full Developer Mode (Currently Active)
**File**: `app/Http/Middleware/DeveloperMode.php`

**Features**:
- ✅ Auto-login as first user (admin@example.com)
- ✅ Set default active location
- ✅ Only active in local environment
- ✅ Security bypass flag enabled
- ✅ All permission checks bypassed

**Security Bypass Implementation**:
```php
// Mark request as developer mode to bypass permission checks
$request->attributes->set('developer_mode_bypass', true);
```

**Custom Permission Middleware**: `app/Http/Middleware/DeveloperPermissionMiddleware.php`
- Extends Spatie PermissionMiddleware
- Checks for developer mode bypass flag
- Logs all bypassed permissions for security awareness
- Falls back to normal permission checks if bypass conditions not met

```php
if (app()->environment('local') && config('app.developer_mode', false)) {
    if (!Auth::check()) {
        $user = User::first();
        Auth::login($user);
        // Set request flag for potential bypass
        $request->attributes->set('developer_mode_bypass', true);
    }
}
```

### Mode 2: Security Bypass Mode (Optional)
**File**: `app/Http/Middleware/DeveloperPermissionMiddleware.php`

**Features**:
- ✅ Auto-login as first user  
- ✅ Set default active location
- ✅ **BYPASS ALL PERMISSION CHECKS** 🚨
- ✅ Security logging of all bypasses
- ✅ Only active in local environment

```php
// Security bypass logic
if (app()->environment('local') && 
    config('app.developer_mode', false) && 
    $request->attributes->get('developer_mode_bypass', false)) {
    
    // LOG BYPASS for security awareness
    \Log::warning('🚨 DEVELOPER MODE: Permission check bypassed', [
        'permission' => $permission,
        'user' => auth()->id(),
        'route' => $request->route()?->getName(),
        'ip' => $request->ip()
    ]);
    
    return $next($request); // BYPASS!
}

// Normal permission check
return parent::handle($request, $next, $permission, $guard);
```

## 🔄 **SWITCHING BETWEEN MODES**

### Activate Basic Mode (Safe)
```php
// In bootstrap/app.php
'permission' => \Spatie\Permission\Middleware\PermissionMiddleware::class,
```

### Activate Security Bypass Mode (⚠️ Dangerous)
```php
// In bootstrap/app.php  
'permission' => \App\Http\Middleware\DeveloperPermissionMiddleware::class,
```

## 🛡️ **SECURITY FEATURES**

### 1. Environment Restrictions
- Only works in `local` environment
- Automatically disabled in `production`
- Requires explicit `DEVELOPER_MODE=true`

### 2. Comprehensive Logging
All bypass activities are logged with:
- Permission that was bypassed
- User ID performing action
- Route name accessed
- IP address of request
- Timestamp of bypass

### 3. Helper Functions
**File**: `app/helpers.php`

```php
// Check if developer mode is active
function developer_mode_active(): bool {
    return app()->environment('local') && config('app.developer_mode', false);
}

// Permission check with developer bypass
function can_with_developer_bypass(string $permission): bool {
    if (developer_mode_active()) {
        return true; // BYPASS
    }
    return auth()->user()?->can($permission) ?? false;
}
```

### 4. Blade Template Usage
```blade
{{-- Use in templates for conditional display --}}
@if(can_with_developer_bypass('products.create'))
    <button>Create Product</button>
@endif

{{-- Or check developer mode directly --}}
@if(developer_mode_active() || auth()->user()->can('products.create'))
    <button>Create Product</button>  
@endif
```

## ⚠️ **CRITICAL SECURITY WARNINGS**

### 🚨 Production Safety
**NEVER ENABLE IN PRODUCTION!**

To ensure safety:
1. Set `APP_ENV=production` in production
2. Set `DEVELOPER_MODE=false` or remove from .env
3. Monitor logs for any bypass messages

### 🚨 Security Implications
When security bypass is active:
- **ALL PERMISSIONS ARE BYPASSED**
- Users can access ANY protected route
- Database operations can be performed without checks
- Admin functions are accessible to any user

### 🚨 Monitoring & Detection
```bash
# Check for bypass activity in logs
grep "DEVELOPER MODE" storage/logs/laravel.log

# Monitor unauthorized access patterns
grep "Permission check bypassed" storage/logs/laravel.log
```

## 🔧 **CONFIGURATION COMMANDS**

### Enable Developer Mode
```powershell
cd "c:\xampp\htdocs\Data IBA POS\IBAPOS"

# Set environment variables
# Edit .env file: DEVELOPER_MODE=true

# Clear cache to apply changes
php artisan config:clear
php artisan cache:clear
```

### Disable Developer Mode
```powershell
# Edit .env file: DEVELOPER_MODE=false
# Or set APP_ENV=production

# Clear cache
php artisan config:clear
php artisan cache:clear
```

### Switch Middleware Mode
```powershell
# Edit bootstrap/app.php
# Change permission middleware class
# Clear cache
php artisan route:clear
php artisan cache:clear
```

## 🧪 **TESTING BYPASS FUNCTIONALITY**

### Test Permission Bypass
1. Login as any user (or let auto-login work)
2. Access restricted route (e.g., `/products` with products.read)
3. Check logs for bypass message:
   ```
   [2025-09-07] local.WARNING: 🚨 DEVELOPER MODE: Permission check bypassed 
   {"permission":"products.read","user":1,"route":"products.index","ip":"127.0.0.1"}
   ```

### Verify Normal Mode
1. Switch to Spatie original middleware
2. Clear cache
3. Access should respect normal permissions

## 📊 **CURRENT CONFIGURATION**

```
Mode: Security Bypass Mode ✅ Active
Environment: local ✅ Safe  
Auto-login: admin@example.com ✅ Working
Permission Bypass: ✅ Active (all permissions bypassed)
Logging: ✅ Active (all bypasses logged)
Production Safety: ✅ Environment-gated
```

## 🔄 **QUICK TOGGLE COMMANDS**

### Enable Full Security Bypass
```powershell
# Quick enable (if not already active)
cd "c:\xampp\htdocs\Data IBA POS\IBAPOS"
# Middleware is already set to DeveloperPermissionMiddleware
php artisan cache:clear
```

### Disable Security Bypass (Keep Auto-login)
```powershell
# Edit bootstrap/app.php - change to:
# 'permission' => \Spatie\Permission\Middleware\PermissionMiddleware::class,
php artisan cache:clear
```

---

**⚡ AI Note**: Developer mode significantly speeds up development but requires careful handling. Use bypass mode only when necessary and always monitor logs for security awareness.
