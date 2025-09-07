# Developer Mode

Developer mode memungkinkan Anda untuk melewati proses authentication saat development, sehingga tidak perlu login berulang kali.

## Fitur

- ✅ **Auto-login** - Otomatis login sebagai user pertama di database
- ✅ **Visual indicator** - Badge "DEV" dan "Developer Mode" di UI
- ✅ **Local only** - Hanya aktif di environment `local`
- ✅ **Easy toggle** - Command untuk mengaktifkan/menonaktifkan
- ✅ **Session management** - Auto-set active location untuk user

## Cara Menggunakan

### Mengaktifkan Developer Mode
```bash
php artisan dev:toggle
```

### Melihat Status
```bash
php artisan dev:toggle --status
```

### Menonaktifkan Developer Mode
```bash
php artisan dev:toggle
```

### Refresh Config (setelah toggle)
```bash
php artisan config:clear
```

## Konfigurasi

Developer mode dikontrol oleh dua setting:

1. **Environment**: Harus `APP_ENV=local`
2. **Developer Mode**: `DEVELOPER_MODE=true` di file `.env`

## Visual Indicators

Saat developer mode aktif, Anda akan melihat:

- Badge "DEV" di sidebar sebelah logo IBAPOS
- Text "Developer Mode" di bawah "Point of Sale"
- Badge "Developer Mode" dengan icon di header

## Keamanan

- Developer mode **HANYA** berfungsi di environment `local`
- Tidak akan berfungsi di `production` atau `staging`
- Middleware akan mengabaikan setting jika environment bukan `local`

## Troubleshooting

### Developer mode tidak berfungsi?
1. Pastikan `APP_ENV=local` di file `.env`
2. Pastikan `DEVELOPER_MODE=true` di file `.env`
3. Jalankan `php artisan config:clear`
4. Pastikan ada user di database

### Visual indicator tidak muncul?
1. Jalankan `npm run build` untuk compile assets
2. Refresh browser (Ctrl+F5)
3. Periksa file `.env` apakah setting sudah benar

## Developer Commands

```bash
# Check status
php artisan dev:toggle --status

# Enable developer mode
php artisan dev:toggle

# Clear config cache
php artisan config:clear

# Build assets
npm run build
```
