# Dokumentasi Print Receipt - IBA POS

## Overview
Fitur Print Receipt telah berhasil diimplementasikan untuk sistem IBA POS. Fitur ini memungkinkan pencetakan struk transaksi yang kompatibel dengan thermal printer 58mm dan 80mm.

## File yang Dimodifikasi/Dibuat

### 1. Template Receipt
**File:** `resources/views/pos/receipt-template.blade.php`
- Template struk yang dioptimalkan untuk thermal printer
- Support ukuran kertas 58mm dan 80mm
- CSS khusus untuk print media
- JavaScript untuk menerima data via postMessage API
- Auto-print setelah menerima data

### 2. Route Print
**File:** `routes/web.php`
- Menambahkan route: `Route::view('/pos/print-receipt', 'pos.receipt-template')`
- Route ini digunakan untuk membuka template receipt di window terpisah

### 3. Checkout Modal Enhancement
**File:** `resources/views/pos/partials/_checkout.blade.php`
- Menambahkan tombol "Print Struk" yang muncul setelah transaksi berhasil
- Tombol terintegrasi dengan state `saleProcessed`
- Icon printer SVG untuk visual yang jelas

### 4. Main POS Integration
**File:** `resources/views/pos/index.blade.php`
- Menambahkan hidden elements untuk store information
- Update Alpine.js pos() function dengan:
  - State `saleProcessed` untuk tracking transaksi
  - State `additional_fee` dan `discount`
  - Function `printStruk()` untuk handling print receipt
- Function `printStruk()` sudah terintegrasi dengan baik dalam sistem yang ada

### 5. File Test
**File:** `test-receipt.html`
- File untuk testing fitur print receipt secara standalone
- Berisi sample data dan tombol test
- Memudahkan troubleshooting dan development

## Cara Kerja Sistem

### 1. Flow Transaksi
1. User melakukan transaksi di POS
2. Setelah checkout berhasil, state `saleProcessed` menjadi `true`
3. Tombol "Print Struk" muncul di checkout modal
4. User klik tombol print

### 2. Flow Print Receipt
1. Function `printStruk()` dipanggil
2. Data transaksi dikumpulkan dari cart dan form
3. Window baru dibuka ke `/pos/print-receipt`
4. Data dikirim via postMessage API
5. Template receipt menerima data dan populate fields
6. Auto-print window terpicu setelah 500ms
7. Window otomatis tertutup setelah print selesai

### 3. Data Structure
```javascript
const receiptData = {
    type: 'RECEIPT_DATA',
    data: {
        store: {
            name: 'IBA POS',
            address: 'Istana Batu Alam',
            phone: 'Telp: 021-12345678'
        },
        trx: {
            date: '13/01/2025 10:30:45',
            no: 'TRX1736745045123',
            buyer: 'Customer Name'
        },
        products: [
            {
                name: 'Produk 1',
                qty: 2,
                price: 50000,
                subtotal: 100000
            }
        ],
        total: 115000,
        payment: 120000,
        change: 5000,
        additional_fee: 15000
    }
}
```

## Fitur Thermal Printer

### 1. Responsive Design
- Default: 58mm width
- Support 80mm dengan media query khusus
- Font: Courier New (monospace) untuk keterbacaan optimal
- Ukuran font disesuaikan untuk thermal printer

### 2. CSS Print Optimization
```css
@media print {
    @page {
        size: 58mm auto;
        margin: 0;
    }
    body {
        margin: 0 !important;
        padding: 0 !important;
    }
}

/* Untuk printer 80mm */
@media print and (min-width: 80mm) {
    @page {
        size: 80mm auto;
    }
    body {
        width: 80mm;
        font-size: 14px;
    }
}
```

### 3. Layout Struktur
- Header: Nama toko, alamat, telepon
- Info transaksi: Tanggal, nomor, kasir, pembeli
- Daftar item: Nama, qty, harga, subtotal
- Summary: Subtotal, biaya tambahan, diskon, total
- Payment: Jumlah bayar, kembalian
- Footer: Ucapan terima kasih

## Testing

### 1. Manual Testing
1. Buka `http://localhost/Data IBA POS/IBAPOS/test-receipt.html`
2. Klik "Test Print Receipt"
3. Verifikasi template receipt muncul dengan data yang benar
4. Test fungsi print browser

### 2. Integration Testing
1. Buka halaman POS: `http://localhost/Data IBA POS/IBAPOS/public/pos`
2. Tambahkan item ke cart
3. Lakukan checkout
4. Klik tombol "Print Struk" setelah transaksi berhasil
5. Verifikasi receipt tercetak dengan benar

## Troubleshooting

### 1. Popup Blocked
- Pastikan browser mengizinkan popup untuk domain ini
- Check browser popup settings

### 2. Print Not Working
- Pastikan printer driver terinstall
- Check browser print settings
- Untuk thermal printer, set custom paper size (58mm x continuous)

### 3. Data Not Showing
- Check browser console untuk error
- Verifikasi postMessage data structure
- Pastikan window.addEventListener('message') berjalan

### 4. CSS Layout Issues
- Test dengan different zoom levels
- Check @media print CSS rules
- Verifikasi printer paper size settings

## Konfigurasi Printer

### Thermal Printer 58mm
- Paper size: Custom 58mm x continuous
- Margins: 0
- Print quality: Draft/Fast

### Thermal Printer 80mm  
- Paper size: Custom 80mm x continuous
- Margins: 0
- Print quality: Draft/Fast

### Regular Printer
- Paper size: A4 atau Letter
- Margins: Minimal
- Preview sebelum print untuk memastikan layout

## Future Enhancements

1. **Database Integration**
   - Save receipt history to database
   - Reprint functionality dari history

2. **Customization**
   - Admin panel untuk edit store information
   - Template customization options
   - Logo upload support

3. **Advanced Features**
   - Barcode/QR code pada receipt
   - Email receipt option
   - SMS receipt notification

4. **Performance**
   - Caching template untuk faster loading
   - Background receipt generation

## Kesimpulan

Fitur Print Receipt telah berhasil diimplementasikan dengan:
- ✅ Template thermal printer responsive (58mm/80mm)
- ✅ Integration dengan sistem POS yang ada
- ✅ PostMessage API untuk data transfer
- ✅ Auto-print functionality
- ✅ Error handling dan validation
- ✅ Testing utilities

Sistem siap untuk production use dan dapat dengan mudah dikustomisasi sesuai kebutuhan bisnis.