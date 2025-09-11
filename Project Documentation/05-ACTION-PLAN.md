# Action Plan – IBA POS (AI-Driven)

Dokumen ini adalah panduan kerja untuk AI agar mengimplementasikan aplikasi secara bertahap, aman, dan terverifikasi. Setiap langkah dilengkapi checklist yang ditandai oleh AI saat selesai.

Catatan sumber kebenaran:
- ERD: `ERD.md` (harus selaras dengan implementasi)
- RBAC: Spatie (roles/permissions) dengan permission per-aksi dan scoping per-lokasi
- Fitur khas: Remote stock pada penjualan (`sale_items.source_location_id`), average costing per lokasi, posting flow draft/received/posted/void

## 0) Sinkronisasi Persyaratan (ERD & Kebijakan)
- [ ] Baca `ERD.md` terbaru dan validasi keberadaan/detil:
  - [ ] sales_payments
  - [ ] stock_ledger
  - [ ] stock_adjustments & stock_adjustment_items
  - [ ] stock_reservations (opsional)
  - [ ] sale_items.source_location_id (FK → locations)
  - [ ] stocks.avg_cost (DECIMAL(18,4))
  - [ ] purchases.status: draft/received/posted/void + received_at/by, posted_at/by, voided_at/by
  - [ ] sales.status: draft/posted/void + posted_at/by, voided_at/by
  - [ ] Soft delete untuk master: products, customers, suppliers
  - [ ] Validasi indeks & constraints minimal:
  - [ ] stocks UNIQUE(product_id, location_id)
  - [ ] sales UNIQUE(location_id, invoice_no)
  - [ ] purchases UNIQUE(location_id, invoice_no)
  - [ ] stock_mutations CHECK(from_location_id <> to_location_id)
  - [ ] Semua FK BIGINT UNSIGNED + INDEX

## 1) Validasi Proyek & Dependensi
- [x] Pastikan Laravel 12 & PHP 8.2 sesuai
- [x] Pastikan paket:
  - [x] spatie/laravel-permission
  - [x] laravel/sanctum
  - [x] tailwind + vite sudah terpasang
- [x] Cek registrasi middleware Spatie di `bootstrap/app.php`

## 2) Migrasi Database (Create/Alter)
- [x] Buat/ubah migrasi agar sesuai ERD (tanpa menghapus data jika sudah ada):
  - [x] Tambah kolom `stocks.avg_cost`
  - [x] Tambah kolom `sale_items.source_location_id` (+index/FK)
  - [x] Tabel `sales_payments`
  - [x] Tabel `stock_ledger`
  - [x] Tabel `stock_adjustments` & `stock_adjustment_items`
  - [x] Tabel `stock_reservations` (opsional; diaktifkan jika perlu)
  - [x] Kolom status & metadata posting pada `sales` & `purchases` (+received_at/by)
  - [x] SoftDeletes pada `products`, `customers`, `suppliers`
  - [x] Indeks & constraints penting (lihat 0)
- [x] Jalankan migrasi secara aman (backup data jika lingkungan berisi data)

## 3) Model & Relasi
- [x] User: HasRoles (Spatie)
- [x] Master: `use SoftDeletes` pada products/customers/suppliers
- [x] Sales:
  - [x] Relasi sale_items (1:M)
  - [x] Relasi sales_payments (1:M)
- [x] SaleItem:
  - [x] Relasi product & sale
  - [x] Relasi `sourceLocation()` → Location
- [x] Stocks: qty, avg_cost casts
- [x] StockLedger: relasi product, location, polymorphic via ref_type/ref_id (non-Eloquent morph; gunakan enum string)
- [x] Purchase/PurchaseItems, StockAdjustments/Items relasi lengkap


- [x] Definisikan taxonomy permission:
  - [x] products.read|create|update|delete
  - [x] categories.*, suppliers.*, customers.* (CRUD)
  - [x] sales.read|create|update|delete|post|void|use_remote_stock
  - [x] purchases.read|create|update|delete|receive|post|void
  - [x] stock_mutations.request|confirm|reject
  - [x] stocks.read|adjust
  - [x] deliveries.read|assign|update_status
  - [x] expenses.read|create|update|delete
  - [x] reports.sales|stock|purchase|finance
  - [x] admin.users|roles|permissions|locations (sesuai kebutuhan)
- [x] Seeder Permissions + Roles awal (super-admin, admin, manager, cashier, warehouse, driver, kepala-gudang)
- [x] Role “kepala-gudang”: akses stok & pembelian (opsional), tanpa akses sales.*

### 2025-09-10 - Role & Permission Management, UI, Modal, AJAX
- CRUD role & permission di UI (controller, view, route) sudah selesai
- Sidebar navigation permission-based sudah aktif
- UI permission grouping & collapsible sudah diterapkan
- Modal create form & AJAX submit sudah terintegrasi
- Konsistensi desain card/tabel/tombol di roles, permissions, users
- Error 500 pada /admin/roles sudah diperbaiki
- Checklist RBAC dan taxonomy permission sudah diupdate

## 5) Scoping Per-Lokasi
- [x] Middleware `ActiveLocation` (pilih lokasi aktif; validasi user ∈ location_user)
- [x] Trait/Global Scope `FiltersByLocation` untuk model berbasis lokasi (sales, purchases, stocks, dsb)
- [x] Pengecualian untuk super-admin

## 6) Layanan Transaksi & Stok (Service Layer)
 - [x] InventoryService
  - [x] getAvailableStock(product, location)
  - [x] reserve/release (opsional, jika stock_reservations diaktifkan) — via ReservationService
  - [x] adjustStockWithLedger(product, location, qtyDelta, costSnapshot)
 - [x] PostingService – Sales
  - [x] Validasi izin `sales.post` (siapkan hook; guard di controller nanti)
  - [x] Lock baris stocks (FOR UPDATE) per (product, source_location)
  - [x] Kurangi stok dari `source_location_id` (atau sales.location_id jika NULL)
  - [x] Hitung COGS = avg_cost sumber × qty
  - [x] Tulis ledger (qty negatif)
  - [x] Sinkron payment dari sales_payments ke sales.payment/change (placeholder; finalize di controller)
  - [x] Set metadata posted_at/by & status=posted
  - [x] Reversal (void) jika ada izin `sales.void`
 - [x] PostingService – Purchases
  - [x] Validasi izin `purchases.post` (siapkan hook; guard di controller nanti)
  - [x] received → posted: hitung landed cost (alokasi freight_cost proporsional berat/nilai) — sederhana dulu; alokasi lanjut
  - [x] Tambah stok; update avg_cost (rata-rata tertimbang)
  - [x] Tulis ledger (qty positif)
  - [x] Set posted_at/by & status=posted
  - [x] Reversal void (kebijakan konsistensi avg_cost dicatat)
 - [x] MutationsService
  - [x] pending → confirmed: satu transaksi atomik; stok turun di asal, naik di tujuan; ledger ±; avg_cost tujuan berbobot
 - [x] AdjustmentsService
  - [x] draft → posted: qty_change ±; ledger; avg_cost naik bila penambahan (unit_cost atau avg saat ini)

## 7) API/Controller & Route Guard
 - [x] Terapkan middleware `permission:` per aksi dan `ActiveLocation`
 - [x] Endpoint POS Sales: dukung source_location_id per item & (opsional) reservation
 - [x] Endpoint Purchases: alur draft → received → posted
 - [x] Endpoint Mutations: request, daftar masuk, confirm/reject
 - [x] Endpoint Adjustments: create, post, void

## 7.1) Location Management Module

Status and completed implementation details are available in the canonical module doc: `12-LOCATION-MODULE.md`.
Refer to that file for the full implementation summary, tests, and next steps.

## 8) UI/UX (Blade + Alpine)
 - [x] Shell admin (Tailwind) — layout dasar siap; menu dinamis oleh permission
 - [x] POS:
  - [x] Draft halaman POS minimal (keranjang, pilih source_location per item)
  - [x] Pencarian produk
  - [x] Indikator asal stok (badge)
  - [x] Diskon per item & validasi stok tersedia
  - [x] Ringkasan pembayaran multi-payment
 - [x] Pembelian: status & posting (minimal list + actions)
 - [x] Mutasi: konfirmasi/penolakan (minimal list + actions)
 - [x] Adjustments: list & post (minimal)
- [ ] Pagination & filter server-side

Catatan: Purchases kini memiliki form create/edit draft, index dengan filter server-side, dan halaman detail dengan aksi Receive/Post/Void; freight_cost sudah dapat diinput (alokasi landed cost dilakukan otomatis di service).

## 9) Notifikasi & PWA
- [ ] Laravel Notifications (database/webpush) & pengaturan notification_settings
- [ ] Service Worker & manifest (cache shell, update strategi)
- [ ] Trigger notifikasi: kasbon, mutasi masuk, status purchase/sale

## 10) Pengujian Otomatis
- [ ] Unit/Feature tests minimal:
  - [x] Sales posted mengurangi stok dan tulis ledger
  - [x] Purchases posted menambah stok & update avg_cost
  - [x] Mutasi stok pending→confirmed memindahkan stok (dua lokasi) & ledger
  - [x] RBAC: user tanpa izin ditolak akses sales
  - [x] Scoping lokasi: data dibatasi ke lokasi user
  - [ ] (Opsional) Reservation: active→consumed/released

- [x] Seed locations, admin user, roles & permissions
- [x] Seed kategori, produk (contoh), stok awal per lokasi

Note: I added a feature test `tests/Feature/ApiLocationsTest.php` which verifies `/api/locations` returns only locations assigned to the authenticated user. I ran `LocationControllerTest` and `LocationScopeTest` plus the new API test locally — all passed in the SQLite test environment.

See also: RBAC guidance in `Catatan Project/03-SPATIE-PERMISSION.md` for permission names and middleware registration.

## 12) Observability & Logging
- [ ] Audit log untuk aksi kritis (post/void/confirm/adjust)
- [ ] Struktur log error yang ramah debugging

## 13) CI/CD & Quality Gates
- [ ] Build: composer install, npm build
- [ ] Lint/Typecheck (PHP-CS-Fixer/larastan opsional)
- [ ] Test: phpunit
- [ ] Gate: gagal bila test gagal atau error static analysis mayor

## 14) Dokumentasi
- [ ] Update `ERD.md` bila ada perubahan skema/constraint
- [ ] Update README tentang RBAC, remote stock, dan alur posting
- [ ] CHANGELOG & PROGRESS harian

---

## Definisi Sukses per Modul
- Sales:
  - [ ] Dapat membuat draft, memilih source_location per item, posting sukses kurangi stok sumber, ledger tercatat, payment sinkron, status=posted
- Purchases:
  - [ ] received→posted update avg_cost dan stok, ledger tercatat
- Mutations:
  - [ ] pending→confirmed memindah stok atomik & ledger ±
- Adjustments:
  - [ ] draft→posted menambah/mengurangi stok sesuai qty_change & ledger
- RBAC/Scoping:
  - [ ] Role kepala-gudang tidak bisa akses sales.*
  - [ ] Semua query data dibatasi sesuai lokasi user kecuali super-admin

## Catatan Operasional AI
- [ ] Selalu lakukan perubahan minimal dan terukur; jalankan migrasi dengan backup jika data sudah ada
- [ ] Setelah edit kode: jalankan Build, Lint (jika ada), Test; perbaiki hingga hijau
- [ ] Jangan menutup pekerjaan dengan kondisi build/test merah
- [ ] Catat perubahan pada CHANGELOG & PROGRESS

## NEXT SESSION: Modal consolidation (postponed)

- Status: postponed to a later session because this change touches many views and needs dedicated time.
- Current priority: focus on Location module improvements only for this session.

- When scheduled: replace duplicated confirmation modals across `resources/views` with `resources/views/components/confirmation-modal.blade.php`.
  - Suggested first targets when resumed: `resources/views/users/*` and `resources/views/products/*` (high duplication & impact).
  - Verify: controllers return JSON for AJAX deletes and add an AJAX-delete feature test per resource.

## 2025-09-11
- Implementasi Kasbon: model, migration, controller, views, route, permission, sidebar.
- Konsistensi layout expense_categories dan expenses.
- Validasi permission dan seed ulang.
- Update dokumentasi dan summary untuk handoff.

