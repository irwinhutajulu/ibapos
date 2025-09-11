# Manual Membuat Notifikasi di IBA POS

Dokumen ini menjelaskan langkah-langkah membuat dan mengirim notifikasi sesuai preferensi user dan role di aplikasi IBA POS.

---

## 1. Menambah Tipe Notifikasi
- Edit file `config/notification.php`.
- Tambahkan tipe notifikasi baru pada array `types`, contoh:
  ```php
  'kasbon_created'   => 'Kasbon Baru',
  'kasbon_approved'  => 'Kasbon Disetujui',
  'kasbon_rejected'  => 'Kasbon Ditolak',
  ```

## 2. Menentukan Penerima Notifikasi
- Untuk notifikasi ke role tertentu:
  ```php
  $admins = User::role(['admin', 'finance'])->get();
  foreach ($admins as $admin) {
      app(NotificationService::class)->sendToUser($admin, 'kasbon_created', $data);
  }
  ```
- Untuk notifikasi ke user tertentu:
  ```php
  $creator = $kasbon->user;
  app(NotificationService::class)->sendToUser($creator, 'kasbon_approved', $data);
  ```

## 3. Mengirim Notifikasi
- Gunakan service `NotificationService::sendToUser`:
  ```php
  app(NotificationService::class)->sendToUser($user, $type, $data);
  ```
- Notifikasi hanya dikirim jika user mengaktifkan tipe/channel terkait di preferensi.

## 4. Contoh Skenario Kasbon
- **User membuat kasbon:**
  - Admin/Finance menerima notifikasi tipe `kasbon_created`.
- **Status kasbon diubah:**
  - User pembuat menerima notifikasi tipe `kasbon_approved` atau `kasbon_rejected`.

## 5. Preferensi Notifikasi User
- User dapat mengatur preferensi notifikasi di menu pengaturan notifikasi.
- Hanya notifikasi yang diaktifkan (channel & tipe) yang akan diterima user.

## 6. Best Practice
- Selalu cek preferensi user sebelum mengirim notifikasi.
- Simpan data notifikasi yang relevan (misal: id kasbon, status, user pembuat).
- Gunakan event/service untuk trigger notifikasi agar terstruktur.

---

**Referensi:**
- `config/notification.php`
- `app/Services/NotificationService.php`
- `app/Models/NotificationSetting.php`
- `app/Models/User.php`
- Event terkait (misal: KasbonCreated, KasbonStatusChanged)
