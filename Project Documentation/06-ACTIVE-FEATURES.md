# ⚡ Active Features - Working Functionality

**Last Updated**: September 14, 2025  
**Purpose**: Complete list of implemented and working features

## 🔧 **DEVELOPMENT ENVIRONMENT**

### Technology Stack
- **Framework**: Laravel 12.28.1, PHP 8.2.12
- **Frontend**: Tailwind CSS v4 via Vite, Alpine.js  
- **Authentication**: Spatie Laravel Permission, Sanctum
- **Database**: MySQL via XAMPP
- **Commands**: PowerShell (Windows environment)

### Developer Mode ✅ Active
- **Auto-login**: Automatic login as admin@example.com
- **Security Bypass**: Optional - can bypass all permission checks
- **Environment**: Local only (`APP_ENV=local`)
- **Configuration**: `DEVELOPER_MODE=true` in .env

## 📱 **RECEIPT PRINTING SYSTEM** ✅ Active

### Thermal Receipt Template
- **Template**: `resources/views/pos/receipt-template.blade.php`
- **Format Support**: 58mm/80mm thermal printers with CSS optimization
- **Data Integration**: PostMessage API for receipt data transfer
- **Store Information**: Dynamic store data from active location
- **Print Features**: Automatic print dialog and receipt formatting

### Location-Based Store Information
- **Complete Store Data**: Name, address, and phone number from active location
- **API Integration**: `/api/locations/{id}` endpoint with phone field support
- **Dynamic Loading**: Store information updates based on location selector
- **Receipt Integration**: Store data automatically included in printed receipts

### Process Sale Verification
- **Sale Verification**: Print button only appears after successful server transaction
- **Server Validation**: Complete stock availability and payment validation
- **State Management**: `saleProcessed` state controls print button visibility
- **UX Enhancement**: Modal stays open after successful sale for immediate printing

## Auth & RBAC
- Login/Logout sederhana; roles & permissions ter-seed (super-admin, admin, manager, cashier, warehouse, driver, kepala-gudang)
- Middleware permission di route utama; sidebar/menu dinamis sesuai permission
- ActiveLocation selector di header; scoping data per-lokasi via FiltersByLocation (kecuali super-admin)

## Database & Domain
- Tabel inti: sales, sale_items (dengan source_location_id), sales_payments
- **Locations**: Enhanced with phone field (nullable string, max 20 chars)
- Purchases + purchase_items (status: draft/received/posted/void + metadata)
- Stocks (qty, avg_cost DECIMAL(18,4)), stock_ledger, stock_adjustments + items, stock_reservations, stock_mutations (CHECK from != to)
- Constraints: UNIQUE stocks(product_id, location_id); UNIQUE sales(location_id, invoice_no); UNIQUE purchases(location_id, invoice_no)
- Soft deletes: products, customers, suppliers

## Services (Business Logic)
- InventoryService: getAvailableStock, adjustStockWithLedger (ledger terjaga)
- SalesPostingService: post/void, kurangi stok sumber, COGS, tulis ledger
- PurchasePostingService: receive/post/void, update avg_cost tertimbang, tulis ledger
- MutationService: confirm/reject, pindah stok atomik + update avg_cost tujuan
- AdjustmentService: post/void, qty_change ±, ledger
- ReservationService: reserve/release/consume (aktif)

## POS & Sales
- POS: pencarian produk, keranjang; qty/harga/diskon per item; pilih source location per item
- Multi-payment (cash/transfer/card/QRIS) dan hitung kembalian
- Validasi stok tersedia: client (batch API) + server-side; remote stock perlu permission sales.use_remote_stock
- Simpan draft sale; endpoint posting & void tersedia
- Live search customer, modal tambah customer baru
- Keyboard shortcuts (F2 pay, F4 discount) [TODO]

## Purchases / Mutations / Adjustments (UI)
- Purchases: create/edit draft, detail show, index dengan filter; aksi Receive/Post/Void
- Supplier live search, modal tambah supplier baru
- Stock Mutations: daftar + Confirm/Reject (pending)
- Stock Adjustments: daftar + Post/Void

## Kasbon Module
- CRUD kasbon: pemohon, lokasi, tanggal, jumlah, status, catatan, approval.
- Sidebar dinamis, permission-based.
- Modal create/edit, partial views, responsive layout.
- Seeder permission kasbons.

## APIs
- GET /api/locations
- GET /api/products?q=
- GET /api/stock/available
- POST /api/stock/available-batch
- GET /api/customers?q=
- GET /api/suppliers?q=

## Seeders
- PermissionsSeeder, AdminSeeder (lokasi + super admin)
- DemoDataSeeder (kategori “Umum”, produk contoh, stok awal lokasi pertama, supplier/customer/purchase demo)

## Testing (Feature)
- Sales posting mengurangi stok & menulis ledger
- Purchase posting menambah stok & update avg_cost
- RBAC guard: akses sales dibatasi permission
- Stock mutation confirm memindahkan stok + ledger
- Location scoping pada sales index
- Validasi server-side stok tidak cukup (HTTP 422)
- Feature test posting, filter, JSON, broadcasting

## Navigasi
- Sidebar kondisional: POS (sales.create), Sales (sales.read), Purchases (purchases.read), Stock Adjustments (stocks.read), Stock Mutations (stock_mutations.request), Reports (placeholder), Settings (admin.users)

## UI/UX
- Dark mode toggle di layout
- Breadcrumbs dan page header unified
- Toast notification realtime (purchase posted/voided, stock updated)
- Modal-based create/edit supplier/customer
- Pagination dan filter di list
- Currency formatting IDR
- Database notification + inbox admin
