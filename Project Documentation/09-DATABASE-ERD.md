# ERD – IBA POS (Ringkasan)

Ringkasan terstruktur dari ERD lengkap (lihat a.ERD.md) untuk memandu implementasi database dan relasi aplikasi POS multi-lokasi.

## Ruang Lingkup
- POS penjualan dengan diskon per item, biaya tambahan, dan jenis pembayaran.
- Manajemen produk (berat & satuan), kategori, stok per lokasi, dan mutasi antar lokasi.
- Pembelian dari supplier (biaya kirim & total berat).
- Pengiriman dari transaksi penjualan (driver/courier).
- RBAC (role, permission), hak akses per lokasi.
- Notifikasi & pengaturan per role/lokasi.
- Biaya (expenses), kategori biaya, dan kasbon pegawai.

## Tabel Utama
- users, roles, role_user
- permissions, permission_role (dengan flag CRUD)
- locations, location_user
- categories, products
- stocks, stock_mutations
- customers, sales, sale_items
- suppliers, purchases, purchase_items
- deliveries
- notifications, notification_settings
- expenses, expense_categories
- kasbons
- sales_payments
- stock_ledger
- stock_adjustments
- stock_adjustment_items
- stock_reservations

## Relasi Kunci
- User ↔ Role: many-to-many (role_user)
- Role ↔ Permission: many-to-many (permission_role; can_create/read/update/delete)
- User ↔ Location: many-to-many (location_user)
- Product ↔ Category: many-to-one
- Stock: stok per product × location (unique pair)
- Sale ↔ SaleItems: one-to-many
- Sale ↔ Customer: many-to-one (nullable)
- Sale ↔ Location: many-to-one; Sale ↔ User: many-to-one
- Purchase ↔ PurchaseItems: one-to-many
- Purchase ↔ Supplier: many-to-one; Purchase ↔ Location: many-to-one; Purchase ↔ User: many-to-one
- Delivery ↔ Sale: one-to-one; Delivery ↔ Location: many-to-one; Delivery ↔ Courier(User): many-to-one
- Notification ↔ User/Role/Location (targeting)
- Expense ↔ ExpenseCategory: many-to-one; Expense ↔ Location/User (creator); recipient_user_id optional
- Kasbon ↔ User (pemohon); approved_by User
- Sale ↔ SalesPayments: one-to-many (multi-payment)
- StockLedger ↔ Product/Location: many-to-one; referensi generik ke sumber transaksi via ref_type + ref_id
- StockAdjustment ↔ StockAdjustmentItems: one-to-many
- StockAdjustment ↔ Location/User (creator): many-to-one
- SaleItem ↔ Source Location: many-to-one (sale_items.source_location_id → locations.id)
- StockReservation ↔ Product/Location/Sale/SaleItem/User: many-to-one

## Field per Tabel (Detail)

Catatan umum:
- Tipe numerik uang: DECIMAL(18,2) unsigned, berat: DECIMAL(10,3) unsigned, kuantitas: DECIMAL(18,3) unsigned bila perlu pecahan.
- Semua FK bertipe BIGINT UNSIGNED dan diindeks; gunakan ON UPDATE CASCADE, ON DELETE sesuai skenario (lihat per tabel).
- Semua tabel transaksi menggunakan timestamps (`created_at`, `updated_at`). Soft delete hanya untuk master bila diperlukan.

### users
- id: BIGINT UNSIGNED, PK, AUTO_INCREMENT
- name: VARCHAR(100), NOT NULL
- email: VARCHAR(191), NOT NULL, UNIQUE
- password: VARCHAR(255), NOT NULL
- remember_token: VARCHAR(100), NULL
- timestamps

Index: UNIQUE(email)

### roles
- id: BIGINT UNSIGNED, PK, AUTO_INCREMENT
- name: VARCHAR(100), NOT NULL, UNIQUE
- description: VARCHAR(255), NULL
- timestamps

### role_user (pivot)
- user_id: BIGINT UNSIGNED, FK → users.id, NOT NULL
- role_id: BIGINT UNSIGNED, FK → roles.id, NOT NULL

Index: UNIQUE(user_id, role_id); INDEX(role_id)
On delete: CASCADE (hapus relasi saat user/role dihapus)

### permissions
- id: BIGINT UNSIGNED, PK, AUTO_INCREMENT
- name: VARCHAR(150), NOT NULL, UNIQUE  // pola feature.action
- description: VARCHAR(255), NULL

### permission_role (pivot, dengan flag CRUD)
- role_id: BIGINT UNSIGNED, FK → roles.id, NOT NULL
- permission_id: BIGINT UNSIGNED, FK → permissions.id, NOT NULL
- can_create: BOOLEAN, DEFAULT 0
- can_read: BOOLEAN, DEFAULT 1
- can_update: BOOLEAN, DEFAULT 0
- can_delete: BOOLEAN, DEFAULT 0

Index: UNIQUE(role_id, permission_id); INDEX(permission_id)
On delete: CASCADE

### locations
- id: BIGINT UNSIGNED, PK, AUTO_INCREMENT
- name: VARCHAR(150), NOT NULL, UNIQUE
- address: VARCHAR(255), NULL
- timestamps

### location_user (pivot)
- user_id: BIGINT UNSIGNED, FK → users.id, NOT NULL
- location_id: BIGINT UNSIGNED, FK → locations.id, NOT NULL

Index: UNIQUE(user_id, location_id); INDEX(location_id)
On delete: CASCADE

### categories
- id: BIGINT UNSIGNED, PK, AUTO_INCREMENT
- name: VARCHAR(150), NOT NULL, UNIQUE
- timestamps

### products
- id: BIGINT UNSIGNED, PK, AUTO_INCREMENT
- name: VARCHAR(191), NOT NULL
- category_id: BIGINT UNSIGNED, FK → categories.id, NOT NULL
- barcode: VARCHAR(64), NULL, UNIQUE
- price: DECIMAL(18,2) UNSIGNED, NOT NULL, DEFAULT 0.00
- weight: DECIMAL(10,3) UNSIGNED, NULL  // kg 
- unit: VARCHAR(20), NULL  // pcs, kg, liter, dll
- timestamps
- deleted_at: TIMESTAMP, NULL  // soft delete

Index: INDEX(category_id); UNIQUE(barcode) (nullable unique)

### stocks (stok per lokasi)
- id: BIGINT UNSIGNED, PK, AUTO_INCREMENT
- product_id: BIGINT UNSIGNED, FK → products.id, NOT NULL
- location_id: BIGINT UNSIGNED, FK → locations.id, NOT NULL
- qty: DECIMAL(18,3) UNSIGNED, NOT NULL, DEFAULT 0.000
- avg_cost: DECIMAL(18,4) UNSIGNED, NOT NULL, DEFAULT 0.0000  // average cost per unit per lokasi
- timestamps

Index: UNIQUE(product_id, location_id); INDEX(location_id)
On delete product/location: RESTRICT (atau CASCADE sesuai kebijakan)

### stock_mutations
- id: BIGINT UNSIGNED, PK, AUTO_INCREMENT
- product_id: BIGINT UNSIGNED, FK → products.id, NOT NULL
- from_location_id: BIGINT UNSIGNED, FK → locations.id, NOT NULL
- to_location_id: BIGINT UNSIGNED, FK → locations.id, NOT NULL
- qty: DECIMAL(18,3) UNSIGNED, NOT NULL
- date: DATE, NOT NULL
- note: VARCHAR(255), NULL
- status: ENUM('pending','confirmed','rejected'), NOT NULL, DEFAULT 'pending'
- requested_by: BIGINT UNSIGNED, FK → users.id, NOT NULL
- confirmed_by: BIGINT UNSIGNED, FK → users.id, NULL
- confirmed_at: TIMESTAMP, NULL
- timestamps

Index: INDEX(product_id); INDEX(from_location_id); INDEX(to_location_id); INDEX(status)
Constraint: CHECK(from_location_id <> to_location_id)

### customers
- id: BIGINT UNSIGNED, PK, AUTO_INCREMENT
- name: VARCHAR(150), NOT NULL
- phone: VARCHAR(30), NULL
- address: VARCHAR(255), NULL
- timestamps
- deleted_at: TIMESTAMP, NULL  // soft delete

Index: INDEX(name), INDEX(phone)

### sales
- id: BIGINT UNSIGNED, PK, AUTO_INCREMENT
- invoice_no: VARCHAR(50), NOT NULL
- date: DATETIME, NOT NULL
- user_id: BIGINT UNSIGNED, FK → users.id, NOT NULL
- location_id: BIGINT UNSIGNED, FK → locations.id, NOT NULL
- customer_id: BIGINT UNSIGNED, FK → customers.id, NULL
- additional_fee: DECIMAL(18,2) UNSIGNED, NULL, DEFAULT 0.00
- discount: DECIMAL(18,2) UNSIGNED, NULL, DEFAULT 0.00  // diskon total transaksi
- total: DECIMAL(18,2) UNSIGNED, NOT NULL, DEFAULT 0.00
- payment: DECIMAL(18,2) UNSIGNED, NOT NULL, DEFAULT 0.00
- change: DECIMAL(18,2) UNSIGNED, NOT NULL, DEFAULT 0.00
- payment_type: VARCHAR(30), NULL  // cash, debit, transfer, e-wallet, dll (bisa enum)
- status: ENUM('draft','posted','void'), NOT NULL, DEFAULT 'draft'  // kontrol posting
- posted_at: DATETIME, NULL
- posted_by: BIGINT UNSIGNED, FK → users.id, NULL
- voided_at: DATETIME, NULL
- voided_by: BIGINT UNSIGNED, FK → users.id, NULL
- timestamps

Index: UNIQUE(location_id, invoice_no); INDEX(date); INDEX(customer_id)

### sale_items
- id: BIGINT UNSIGNED, PK, AUTO_INCREMENT
- sale_id: BIGINT UNSIGNED, FK → sales.id, NOT NULL
- product_id: BIGINT UNSIGNED, FK → products.id, NOT NULL
- qty: DECIMAL(18,3) UNSIGNED, NOT NULL
- price: DECIMAL(18,2) UNSIGNED, NOT NULL
- discount: DECIMAL(18,2) UNSIGNED, NULL, DEFAULT 0.00  // diskon per item (nominal)
- subtotal: DECIMAL(18,2) UNSIGNED, NOT NULL  // (price - discount) * qty (dimaterialisasi)
- source_location_id: BIGINT UNSIGNED, FK → locations.id, NULL  // sumber stok (jika berbeda dari sales.location_id)

Index: INDEX(sale_id); INDEX(product_id); INDEX(source_location_id)
On delete sale: CASCADE (hapus detail saat header dihapus)

### suppliers
- id: BIGINT UNSIGNED, PK, AUTO_INCREMENT
- name: VARCHAR(150), NOT NULL
- phone: VARCHAR(30), NULL
- address: VARCHAR(255), NULL
- timestamps
- deleted_at: TIMESTAMP, NULL  // soft delete

Index: INDEX(name), INDEX(phone)

### purchases
- id: BIGINT UNSIGNED, PK, AUTO_INCREMENT
- invoice_no: VARCHAR(50), NOT NULL
- date: DATETIME, NOT NULL
- user_id: BIGINT UNSIGNED, FK → users.id, NOT NULL
- location_id: BIGINT UNSIGNED, FK → locations.id, NOT NULL
- supplier_id: BIGINT UNSIGNED, FK → suppliers.id, NOT NULL
- total: DECIMAL(18,2) UNSIGNED, NOT NULL, DEFAULT 0.00
- total_weight: DECIMAL(18,3) UNSIGNED, NULL, DEFAULT 0.000
- freight_cost: DECIMAL(18,2) UNSIGNED, NULL, DEFAULT 0.00
- status: ENUM('draft','received','posted','void'), NOT NULL, DEFAULT 'draft'  // kontrol posting (received = barang diterima)
- received_at: DATETIME, NULL  // tanggal/ waktu barang diterima
- received_by: BIGINT UNSIGNED, FK → users.id, NULL
- posted_at: DATETIME, NULL
- posted_by: BIGINT UNSIGNED, FK → users.id, NULL
- voided_at: DATETIME, NULL
- voided_by: BIGINT UNSIGNED, FK → users.id, NULL
- timestamps

Index: UNIQUE(location_id, invoice_no); INDEX(date); INDEX(supplier_id)

### purchase_items
- id: BIGINT UNSIGNED, PK, AUTO_INCREMENT
- purchase_id: BIGINT UNSIGNED, FK → purchases.id, NOT NULL
- product_id: BIGINT UNSIGNED, FK → products.id, NOT NULL
- qty: DECIMAL(18,3) UNSIGNED, NOT NULL
- price: DECIMAL(18,2) UNSIGNED, NOT NULL
- subtotal: DECIMAL(18,2) UNSIGNED, NOT NULL

Index: INDEX(purchase_id); INDEX(product_id)
On delete purchase: CASCADE

### sales_payments
- id: BIGINT UNSIGNED, PK, AUTO_INCREMENT
- sale_id: BIGINT UNSIGNED, FK → sales.id, NOT NULL
- type: VARCHAR(30), NOT NULL  // cash, debit, transfer, e-wallet, other (atau enum)
- amount: DECIMAL(18,2) UNSIGNED, NOT NULL
- reference: VARCHAR(100), NULL  // no kartu/VA/ref transaksi
- note: VARCHAR(255), NULL
- paid_at: DATETIME, NOT NULL, DEFAULT CURRENT_TIMESTAMP
- timestamps

Index: INDEX(sale_id); INDEX(type); INDEX(paid_at)
On delete sale: CASCADE

### stock_ledger
- id: BIGINT UNSIGNED, PK, AUTO_INCREMENT
- product_id: BIGINT UNSIGNED, FK → products.id, NOT NULL
- location_id: BIGINT UNSIGNED, FK → locations.id, NOT NULL
- ref_type: VARCHAR(50), NOT NULL  // 'sale','purchase','stock_mutation','stock_adjustment'
- ref_id: BIGINT UNSIGNED, NOT NULL
- qty_change: DECIMAL(18,3), NOT NULL  // positif untuk masuk, negatif untuk keluar
- balance_after: DECIMAL(18,3) UNSIGNED, NOT NULL  // saldo stok setelah transaksi
- cost_per_unit_at_time: DECIMAL(18,4) UNSIGNED, NULL  // snapshot avg_cost saat itu
- total_cost_effect: DECIMAL(18,2) NULL  // untuk analisis COGS/landed cost
- user_id: BIGINT UNSIGNED, FK → users.id, NULL
- note: VARCHAR(255), NULL
- created_at: TIMESTAMP, NOT NULL, DEFAULT CURRENT_TIMESTAMP

Index: INDEX(product_id, location_id); INDEX(ref_type, ref_id); INDEX(created_at)

### stock_adjustments
- id: BIGINT UNSIGNED, PK, AUTO_INCREMENT
- code: VARCHAR(30), NULL  // nomor referensi/opsional
- date: DATETIME, NOT NULL
- location_id: BIGINT UNSIGNED, FK → locations.id, NOT NULL
- user_id: BIGINT UNSIGNED, FK → users.id, NOT NULL  // pembuat
- reason: VARCHAR(50), NULL  // cycle_count, damage, spoilage, theft, other
- note: VARCHAR(255), NULL
- status: ENUM('draft','posted','void'), NOT NULL, DEFAULT 'draft'
- posted_at: DATETIME, NULL
- posted_by: BIGINT UNSIGNED, FK → users.id, NULL
- voided_at: DATETIME, NULL
- voided_by: BIGINT UNSIGNED, FK → users.id, NULL
- timestamps

Index: INDEX(location_id); INDEX(user_id); INDEX(status); UNIQUE(location_id, code)

### stock_adjustment_items
- id: BIGINT UNSIGNED, PK, AUTO_INCREMENT
- stock_adjustment_id: BIGINT UNSIGNED, FK → stock_adjustments.id, NOT NULL
- product_id: BIGINT UNSIGNED, FK → products.id, NOT NULL
- qty_change: DECIMAL(18,3), NOT NULL  // + ditemukan, - hilang/rusak
- unit_cost: DECIMAL(18,4) UNSIGNED, NULL  // opsional; jika NULL gunakan avg_cost saat posting
- note: VARCHAR(255), NULL

Index: INDEX(stock_adjustment_id); INDEX(product_id)
On delete stock_adjustment: CASCADE

### stock_reservations (opsional, untuk hold stok saat draft)
- id: BIGINT UNSIGNED, PK, AUTO_INCREMENT
- product_id: BIGINT UNSIGNED, FK → products.id, NOT NULL
- location_id: BIGINT UNSIGNED, FK → locations.id, NOT NULL  // lokasi sumber stok yang di-hold
- sale_id: BIGINT UNSIGNED, FK → sales.id, NOT NULL
- sale_item_id: BIGINT UNSIGNED, FK → sale_items.id, NULL
- qty_reserved: DECIMAL(18,3) UNSIGNED, NOT NULL
- status: ENUM('active','consumed','released','expired'), NOT NULL, DEFAULT 'active'
- expires_at: DATETIME, NULL
- created_by: BIGINT UNSIGNED, FK → users.id, NOT NULL
- released_at: DATETIME, NULL
- released_by: BIGINT UNSIGNED, FK → users.id, NULL
- consumed_at: DATETIME, NULL
- consumed_by: BIGINT UNSIGNED, FK → users.id, NULL
- created_at: TIMESTAMP, NOT NULL, DEFAULT CURRENT_TIMESTAMP

Index: INDEX(product_id, location_id); INDEX(sale_id); INDEX(status); INDEX(expires_at)

### deliveries
- id: BIGINT UNSIGNED, PK, AUTO_INCREMENT
- sale_id: BIGINT UNSIGNED, FK → sales.id, NOT NULL, UNIQUE  // satu pengiriman per sale (opsi)
- date: DATETIME, NOT NULL
- deadline_date: DATE, NULL
- deadline_time: TIME, NULL
- receiver_name: VARCHAR(150), NULL
- address: VARCHAR(255), NULL
- phone: VARCHAR(30), NULL
- courier_id: BIGINT UNSIGNED, FK → users.id, NULL  // driver/supir
- status: VARCHAR(30), NOT NULL, DEFAULT 'processed'  // processed, shipped, delivered, canceled
- note: VARCHAR(255), NULL
- location_id: BIGINT UNSIGNED, FK → locations.id, NOT NULL
- timestamps

Index: INDEX(status); INDEX(courier_id); INDEX(location_id)

### notifications
- id: BIGINT UNSIGNED, PK, AUTO_INCREMENT
- type: VARCHAR(100), NOT NULL
- title: VARCHAR(150), NOT NULL
- message: TEXT, NOT NULL
- user_id: BIGINT UNSIGNED, FK → users.id, NULL
- role_id: BIGINT UNSIGNED, FK → roles.id, NULL
- location_id: BIGINT UNSIGNED, FK → locations.id, NULL
- is_read: BOOLEAN, NOT NULL, DEFAULT 0
- created_at: TIMESTAMP, NOT NULL, DEFAULT CURRENT_TIMESTAMP

Index: INDEX(user_id), INDEX(role_id), INDEX(location_id), INDEX(is_read)

### notification_settings
- id: BIGINT UNSIGNED, PK, AUTO_INCREMENT
- notification_type: VARCHAR(100), NOT NULL
- role_id: BIGINT UNSIGNED, FK → roles.id, NOT NULL
- location_id: BIGINT UNSIGNED, FK → locations.id, NULL
- enabled: BOOLEAN, NOT NULL, DEFAULT 1

Index: UNIQUE(notification_type, role_id, location_id)

### expenses
- id: BIGINT UNSIGNED, PK, AUTO_INCREMENT
- date: DATE, NOT NULL
- location_id: BIGINT UNSIGNED, FK → locations.id, NOT NULL
- user_id: BIGINT UNSIGNED, FK → users.id, NOT NULL  // pencatat
- recipient_user_id: BIGINT UNSIGNED, FK → users.id, NULL  // penerima (gaji/kasbon)
- category_id: BIGINT UNSIGNED, FK → expense_categories.id, NOT NULL
- amount: DECIMAL(18,2) UNSIGNED, NOT NULL
- description: VARCHAR(255), NULL
- timestamps

Index: INDEX(date); INDEX(location_id); INDEX(category_id); INDEX(recipient_user_id)

### expense_categories
- id: BIGINT UNSIGNED, PK, AUTO_INCREMENT
- name: VARCHAR(150), NOT NULL, UNIQUE
- timestamps

### kasbons
- id: BIGINT UNSIGNED, PK, AUTO_INCREMENT
- user_id: BIGINT UNSIGNED, FK → users.id, NOT NULL  // pemohon
- date: DATE, NOT NULL
- amount: DECIMAL(18,2) UNSIGNED, NOT NULL
- description: VARCHAR(255), NULL
- status: ENUM('pending','approved','rejected','paid'), NOT NULL, DEFAULT 'pending'
- approved_by: BIGINT UNSIGNED, FK → users.id, NULL
- approved_at: TIMESTAMP, NULL
- paid_at: TIMESTAMP, NULL
- timestamps

Index: INDEX(user_id); INDEX(status); INDEX(approved_by)

## Catatan & Perilaku Bisnis
- Permission mengikuti pola "feature.action" (mis. product.read, product.create). Dapat digenerate otomatis dari controller/route.
- Middleware/policy mengecek permission dan membatasi akses data per-lokasi.
- Transaksi penjualan/pembelian/pengiriman selalu terkait lokasi; data pembeli pada sales opsional.
- Mutasi stok: harus dikonfirmasi oleh lokasi tujuan sebelum stok berpindah (status pending → confirmed/rejected).
- Pembelian menyimpan total berat dan biaya pengiriman; produk punya berat dan satuan.
- Penjualan: biaya tambahan (additional_fee), diskon total (discount), diskon per item (sale_items.discount), jenis pembayaran (payment_type).
- Kontrol posting (sales, purchases): status draft/posted/void.
- posted: mempengaruhi stok dan menulis ke stock_ledger; draft: belum mempengaruhi stok; void: membalik efek posted (reversal) bila memungkinkan.
- Multi-payment: gunakan sales_payments untuk menyimpan beberapa metode/nominal pembayaran satu transaksi.
- Costing average per lokasi:
     - avg_cost disimpan di stocks dan diperbarui saat purchase berstatus posted (freight_cost dialokasikan proporsional berat atau nilai ke item → landed cost);
	 - mutasi antar lokasi: tujuan menghitung avg_cost baru berbobot; asal tidak mengubah avg_cost, hanya mengurangi qty;
	 - penjualan menggunakan avg_cost saat posting untuk COGS; semua perubahan stok tercatat di stock_ledger.
	- Penyesuaian stok: gunakan stock_adjustments (+item/-item). Saat posted, tulis stock_ledger dan perbarui qty; untuk penambahan dapat memilih unit_cost (jika kosong, gunakan avg_cost berjalan) sehingga avg_cost bisa diperbarui secara wajar.
	- Remote stock penjualan: sale_items dapat mengambil stok dari lokasi lain via source_location_id (butuh izin khusus & akses lokasi sumber). Opsi reservation tersedia melalui stock_reservations untuk menahan stok saat draft.

## Regional/Localization
- Region Indonesia: tanggal (dd-mm-yyyy / yyyy-mm-dd), mata uang Rupiah, berat gram/kg, bahasa Indonesia.

## Catatan Implementasi
 - Validasi dan tampilan mengikuti standar lokal.
 - Soft deletes: hanya untuk master (products, customers, suppliers). Transaksi tidak dihapus — gunakan status (draft/posted/void).
 - Multi-pembayaran: tabel sales_payments adalah sumber utama; field payment/change pada sales dapat disinkronisasi sebagai cache.
 - Costing average: simpan avg_cost pada stocks; update atomik saat purchase/transfer posted; alokasikan freight_cost proporsional ke item.
 - Stock ledger: catat setiap perubahan stok dengan ref_type+ref_id, qty_change, balance_after, dan snapshot biaya.
 - Konsistensi: gunakan transaksi DB + row-level locking saat posting penjualan/pembelian/mutasi untuk mencegah over-sell dan race condition.

## Alur Otomatisasi Posting (Ringkas)

### Purchases
- draft → received: hanya mencatat waktu/aktor penerimaan (received_at/by); stok belum berubah.
- received → posted:
	1) Hitung landed cost per item (alokasi freight_cost proporsional berat atau nilai).
	2) Tambah qty ke stocks; hitung avg_cost baru per lokasi dengan rumus rata-rata tertimbang:
		 avg_cost_new = (qty_old*avg_cost_old + qty_in*cost_in) / (qty_old + qty_in)
	3) Tulis stock_ledger (qty_change positif, balance_after, cost_per_unit_at_time).
	4) Set posted_at/by, status=posted.
- posted → void:
	- Jika memungkinkan dan qty mencukupi, lakukan reversal: kurangi stok sebesar qty masuk; update avg_cost dengan pendekatan rollback (atau catat adjustment khusus); tulis ledger negatif; set voided_at/by.

### Sales
- draft → posted:
	1) Validasi stok tersedia; lock baris stocks terkait.
	2) Kurangi qty dari stocks; COGS = avg_cost saat posting × qty keluar (per item).
	3) Tulis stock_ledger (qty_change negatif) dan agregasi COGS ke analitik bila diperlukan.
	4) Agregasi pembayaran dari sales_payments; sinkronkan field payment/change di sales.
	5) Set posted_at/by, status=posted.
- posted → void:
	- Reversal jika kebijakan mengizinkan: kembalikan stok; ledger positif; tandai voided_at/by.

### Stock Mutations
- pending → confirmed:
	- Dalam satu transaksi atomik: kurangi stok lokasi asal, tambah stok lokasi tujuan; ledger 2 baris (negatif asal, positif tujuan). Update avg_cost tujuan berbobot dari cost asal.

Catatan: Bila reversal kompleks (terutama average cost), pertimbangkan adjustment khusus dibanding menghitung ulang seluruh histori.

### Stock Adjustments
- draft → posted:
	1) Untuk setiap item: jika qty_change > 0 (penambahan), tambah qty ke stocks; tentukan cost_in = unit_cost jika ada, selain itu gunakan avg_cost saat ini; hitung avg_cost baru berbobot.
	2) Jika qty_change < 0 (pengurangan), kurangi qty; COGS=avg_cost saat ini; avg_cost tidak berubah. 
	3) Tulis stock_ledger per item (qty_change ±, balance_after, cost_per_unit_at_time).
	4) Set posted_at/by, status=posted.
- posted → void: reversal stok dan ledger jika diizinkan.

### Sales Remote Stock & Reservations (Opsional)
- Jika remote stock diaktifkan dan user memiliki izin sales.use_remote_stock:
	1) Pada draft, user memilih source_location_id per item (hanya lokasi yang user akses dan stok>0).
	2) (Opsional) Sistem membuat stock_reservations untuk menahan qty di lokasi sumber; reservation dapat kadaluarsa otomatis.
	3) Saat posting, kurangi stok dari source_location_id (bukan sales.location_id bila berbeda), gunakan avg_cost lokasi sumber untuk COGS, tulis ledger.
	4) Konsumsi reservation (status=consumed) atau rilis saat void/batal (status=released/expired).

---
Dokumen ini merangkum ERD yang ada untuk memudahkan pengembangan tanpa mengubah cakupan fitur yang sudah ditetapkan.
