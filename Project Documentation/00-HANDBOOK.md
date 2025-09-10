# 2025-09-10
- Semua toast notification kini pakai partial _flash_notify, dipanggil di setiap view utama
- Script notify harus dipush ke Blade stack agar tidak error
- Layout app.blade.php sudah memuat Alpine.js dan container toast
- Semua perubahan UI mengikuti theme modern (Tailwind, dark mode)
````markdown
# Catatan Project - Handbook (TOC)

Ringkasan singkat dan lokasi dokumen kanonis di folder `Catatan Project`.

Tujuan: menyediakan satu halaman referensi singkat yang menjelaskan peran tiap dokumen sehingga AI/developer tahu file mana yang dijadikan sumber kebenaran.

## Canonical documents (one-line each)
- `00-PROJECT-CONFIG.md` — Konfigurasi proyek, commands & environment (BACA PERTAMA)
- `01-SYSTEM-STATUS.md` — Snapshot sistem: apa yang bekerja dan apa yang tidak
- `02-DEVELOPER-MODE.md` — Penjelasan developer-mode dan implikasi keamanan
- `03-SPATIE-PERMISSION.md` — RBAC / Spatie Permission (CANONICAL for permissions)
- `04-ISSUE-RESOLUTION.md` — Panduan solusi dan keputusan teknis penting
- `05-ACTION-PLAN.md` — Roadmap & task checklist (WHAT to do)
- `06-ACTIVE-FEATURES.md` — Ringkasan fitur aktif
- `07-PROGRESS.md` — Log harian / progres (WHAT was done)
- `08-CHANGELOG.md` — Riwayat perubahan (release-style)
- `09-DATABASE-ERD.md` — Skema DB / ERD (CANONICAL for DB)
- `10-UI-COMPONENTS.md` — UI components & patterns (CANONICAL for UI)
- `12-LOCATION-MODULE.md` — Modul Location (CANONICAL for Location feature)

## Quick guidance
- When in doubt about permissions or roles, read `03-SPATIE-PERMISSION.md` first.
- For UI component implementation or migration, read `10-UI-COMPONENTS.md` (it contains merged modal & layout guidance).
- Use `05-ACTION-PLAN.md` to pick next tasks; record daily achievements into `07-PROGRESS.md`.

## Housekeeping rules
- Keep a single canonical file per topic; remove or archive duplicates.
- When merging notes, update `00-HANDBOOK.md` and add a short entry in `07-PROGRESS.md` describing the consolidation.

-- End of handbook
````
