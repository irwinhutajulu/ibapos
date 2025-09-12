# POS Default Location Update - September 12, 2025

## Perubahan yang Diterapkan

### 1. **Default Source Location pada Add to Cart**
- **Before**: `source_location_id: null`
- **After**: `source_location_id: window.appActiveLocationId || null`

Ketika produk ditambahkan ke cart, secara otomatis akan menggunakan lokasi aktif sebagai source location default.

### 2. **Source Location Dropdown Ordering**
- **Before**: "Active Location" sebagai option pertama dengan value `null`
- **After**: Lokasi aktual ditampilkan pertama dengan lokasi aktif sebagai default yang terpilih

### 3. **Badge Location Display**
- **Before**: Menampilkan "Active Location" jika tidak ditemukan
- **After**: Menampilkan "Unknown Location" untuk handling edge case yang lebih jelas

## Code Changes

### JavaScript addToCart Function
```javascript
// OLD
source_location_id: null,

// NEW  
source_location_id: window.appActiveLocationId || null,
```

### Source Location Dropdown
```html
<!-- OLD -->
<option :value="null">Active Location</option>
<template x-for="loc in locations" :key="loc.id">
  <option :value="loc.id" ...></option>
</template>

<!-- NEW -->
<template x-for="loc in locations" :key="loc.id">
  <option :value="loc.id" :selected="loc.id === window.appActiveLocationId" ...></option>
</template>
<option :value="null" :selected="!window.appActiveLocationId">Other Location</option>
```

### Badge Display Update
```javascript
// OLD
'Active Location'

// NEW
'Unknown Location'
```

## Expected Behavior

1. **Saat produk ditambahkan ke cart**:
   - Source location otomatis akan diset ke lokasi aktif dari location selector
   - Badge akan menampilkan nama lokasi aktif

2. **Pada dropdown source location**:
   - Lokasi aktif akan menjadi pilihan yang terpilih secara default
   - Urutan: Lokasi aktual terlebih dahulu, kemudian "Other Location"

3. **Validasi stock**:
   - Stock akan diperiksa berdasarkan lokasi yang dipilih
   - Error message akan lebih jelas menunjukkan lokasi spesifik

## Testing

Untuk menguji perubahan ini:

1. **Login ke POS**
2. **Pilih lokasi aktif dari location selector**
3. **Search dan add produk ke cart**
4. **Verify**:
   - Source location badge menampilkan nama lokasi aktif
   - Dropdown source location memiliki lokasi aktif sebagai default
   - Stock validation menggunakan lokasi yang benar

## Files Modified

- `resources/views/pos/index.blade.php` - Main POS interface updates

---
**Updated by**: GitHub Copilot  
**Date**: September 12, 2025  
**Feature**: Default Source Location to Active Location