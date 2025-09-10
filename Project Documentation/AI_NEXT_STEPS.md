# AI Next Steps — Catatan Project (merged)

Lokasi: `Catatan Project/AI_NEXT_STEPS.md`

Tujuan: file ini adalah versi kanonis dari catatan "next steps" yang sebelumnya ada di project root. Berikut instruksi singkat dan daftar tugas untuk sesi AI/dev berikutnya.

## Requirements checklist (carry-over)
- [x] Konsolidasi README dan AI handoff ke folder `Catatan Project/`
- [x] Tanda bahwa file root telah digabungkan ke `Catatan Project/`
- [ ] Jalankan test suite: `php artisan test` dan perbaiki kegagalan
- [ ] Verifikasi middleware `app/Http/Middleware/DeveloperPermissionMiddleware.php` (unit + runtime)
- [ ] Audit `routes/web.php` untuk penggunaan `Request::get()` yang salah
- [ ] Pastikan seeders membuat `admin@example.com` dan `super-admin` role

## Quick commands (PowerShell)

```powershell
cd "c:\xampp\htdocs\Data IBA POS\IBAPOS"
composer install; npm install
copy .env.example .env
php artisan key:generate
php artisan migrate --graceful
php artisan db:seed --class=DatabaseSeeder
php artisan test
```

## Files to update in this folder (priority)
1. `00-PROJECT-CONFIG.md` — ensure commands, paths, and Developer Mode notes are canonical
2. `01-SYSTEM-STATUS.md` — update with latest smoke-test results (middleware, routes, views)
3. `03-SPATIE-PERMISSION.md` — add notes about `DeveloperPermissionMiddleware` and aliasing in `bootstrap/app.php`
4. `07-PROGRESS.md` — append today's changes and verification steps (see bottom of this file)

## Guidance for next AI session
- Always update or add details under `Catatan Project/` first. That folder is the single source of truth for AI handoffs.
- When changing middleware or packages, also append a short note to `07-PROGRESS.md` describing the change, the files edited, and commands run (e.g., `composer dump-autoload`, `php artisan config:clear`).
- For any runtime fixes, include the exact `php` and `artisan` commands used and the tail of `storage/logs/laravel.log` showing errors and confirmation of resolution.

## Edge cases & checks
- If the environment is Windows and paths include spaces, quote paths as in examples.
- If DB not available, use `database/database.sqlite` and update `.env` (set `DB_CONNECTION=sqlite` and `DB_DATABASE=database/database.sqlite`).

---

## Progress note (append to `07-PROGRESS.md`)

- 2025-09-10: Merged root `README.md` and `AI_NEXT_STEPS.md` into `Catatan Project/AI_NEXT_STEPS.md`. Root README kept as convenience copy but canonical notes now live under `Catatan Project/`.
- Actions performed: reviewed Spatie permission setup, edited `app/Http/Middleware/DeveloperPermissionMiddleware.php` to fix signature mismatch, ran `composer dump-autoload` and cleared Laravel caches.
- Next verification: run `php artisan serve` and open a `permission:`-protected route; check `storage/logs/laravel.log` for new fatal errors.
