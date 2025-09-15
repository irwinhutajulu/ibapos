# Print Receipt - XAMPP Setup Guide

## URL Akses untuk XAMPP

### Halaman Utama POS:
```
http://localhost/IBAPOS/public/pos
```

### Template Receipt:
```
http://localhost/IBAPOS/public/pos/print-receipt
```

### Test Page:
```
http://localhost/IBAPOS/public/test-receipt.html
```

## Cara Testing Print Receipt

### 1. Testing Manual via Test Page
1. Buka: `http://localhost/IBAPOS/public/test-receipt.html`
2. Klik tombol "Test Print Receipt" 
3. Window baru akan terbuka dengan template receipt
4. Data contoh akan otomatis ter-populate
5. Print dialog akan muncul otomatis

### 2. Testing via POS System
1. Buka: `http://localhost/IBAPOS/public/pos`
2. Login jika diperlukan
3. Tambahkan produk ke cart
4. Lakukan checkout (isi payment)
5. Setelah transaksi berhasil, tombol "Print Struk" akan muncul
6. Klik tombol tersebut untuk print receipt

## Konfigurasi Thermal Printer

### Printer Settings:
- **Paper Size**: Custom 58mm atau 80mm width
- **Margins**: 0 (zero margins)
- **Print Quality**: Draft/Fast untuk thermal printer
- **Page Setup**: Portrait orientation

### Browser Print Settings:
- Pastikan "Print backgrounds" dicentang
- Set margins ke minimum (0)
- Pilih paper size "More settings" → "Custom" → Width: 58mm

## Troubleshooting

### Jika Popup Tidak Muncul:
- Pastikan popup blocker dinonaktifkan untuk localhost
- Cek console browser untuk error messages

### Jika Template Kosong:
- Periksa JavaScript console untuk error
- Pastikan postMessage communication berfungsi

### Jika Print Format Tidak Benar:
- Adjust CSS di `resources/views/pos/receipt-template.blade.php`
- Modify @media print rules untuk ukuran kertas yang berbeda

## File-file Penting

1. **Route**: `routes/web.php` - Definisi route `/pos/print-receipt`
2. **Template**: `resources/views/pos/receipt-template.blade.php` - Template receipt
3. **POS Main**: `resources/views/pos/index.blade.php` - Interface POS utama
4. **Checkout**: `resources/views/pos/partials/_checkout.blade.php` - Modal checkout dengan tombol print

## Status: ✅ Ready for Production

Sistem print receipt sudah siap digunakan dengan XAMPP setup!