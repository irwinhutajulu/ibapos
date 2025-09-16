# Invoice Generator (IBA) - Implementation Notes

Status: Implemented (2025-09-16)

This document describes the invoice number generator added to the project to automatically create `invoice_no` values for `sales` when they are missing during creation or import.

## Goal
- Provide a safe, concurrency-resistant way to generate invoice numbers.
- Invoice must contain `IBA` and `location_id`.
- Avoid race conditions during imports or concurrent requests.

## Implemented Format
- `IBA-{location_id}-{YYYYMMDD}-{000001}`
  - Example: `IBA-1-20250916-000001`
  - Sequence resets daily per-location (by design). If you want a global or yearly sequence, adjust the `invoice_counters` migration and service.

## Files Added
- Migration: `database/migrations/2025_09_16_120000_create_invoice_counters_table.php`
  - Creates `invoice_counters` with columns: `id`, `type`, `location_id`, `last_number`, `date`, timestamps
  - Unique index: `type + location_id + date` to support daily per-location counters.

- Model: `app/Models/InvoiceCounter.php`
  - Simple Eloquent model with fillable: `type, location_id, last_number, date`.

- Service: `app/Services/InvoiceGenerator.php`
  - Public static method: `next(string $type, ?int $locationId = null): string`.
  - Implementation details:
    - Uses `DB::transaction()` and `lockForUpdate()` on `invoice_counters` row to increment `last_number` safely.
    - Calculates sequence and returns formatted invoice string.

- Controller update: `app/Http/Controllers/SalesController.php`
  - When `invoice_no` is missing in `store()` data, controller now calls:
    ```php
    \App\Services\InvoiceGenerator::next('sale', $locationId);
    ```
  - Ensures insert into `sales` always has `invoice_no` (migration requires it non-null and unique per location).

## Usage
- Run migration:

```powershell
php artisan migrate
```

- Creating sales via UI/API without `invoice_no` will now automatically receive a generated invoice number.

- Importer: when implemented, importer must call `InvoiceGenerator::next('sale', $locationId)` for rows where `invoice_no` is empty before inserting rows into `sales` table.

## Backfill strategy (existing rows with null invoice_no)
- Run an artisan command or tinker script to assign invoice numbers to existing sales missing `invoice_no`:

```php
Sale::whereNull('invoice_no')->chunk(100, function ($rows) {
    foreach ($rows as $sale) {
        $sale->invoice_no = \App\Services\InvoiceGenerator::next('sale', $sale->location_id);
        $sale->save();
    }
});
```

Note: Backfill will use the daily counter at runtime (so numbers will be generated using the current date). If you need to preserve original dates for sequencing, modify `InvoiceGenerator` to accept an optional date parameter.

## Alternatives & Notes
- Current implementation resets sequence daily (date partition). If you prefer not to reset daily, remove `date` column/unique key or modify migration accordingly.
- If you prefer `location_code` (e.g., `IBA-MAIN-...`) instead of numeric `location_id`, change the format in `InvoiceGenerator::next()` accordingly and ensure no forbidden characters.
- The service uses DB locking; ensure your DB supports row-level locking (MySQL/InnoDB do). On SQLite this locking works differently — tests might behave differently in SQLite.

## Testing suggestions
- Add concurrent test to ensure uniqueness when multiple jobs request invoice numbers simultaneously.
- Add unit test asserting format and that daily reset works (create counters with different dates and verify sequence).

## Integration notes
- `SalesController` updated to call generator on missing invoice. If other code inserts sales directly (factories, seeders, direct DB inserts), ensure they either provide invoice_no or call the generator.
- Importer (multi-sheet XLSX) should follow same rule and call generator before inserting.

## Import pipeline and session notes (2025-09-16 → 2025-09-17)
This project session added a small import pipeline and made several importer fixes to support bulk Excel/CSV imports while preserving explicit IDs and timestamps and auto-generating invoice numbers when missing.

Files and scripts added for the import workflow (helpers / temporary):
- `scripts/merge_create_only.php` — merges three template CSVs (sales, sales_items, sales_payments) into a single per-sale merged CSV. Produces a temp file path like `sales_import_merged_<uniqid>.csv`.
- `scripts/merge_and_dryrun_import.php` — convenience wrapper that creates merged CSV then runs `php artisan import:sales --dry-run "<merged.csv>"` and prints results.
- `scripts/scan_merged_csv.php` — scanner that validates the merged CSV for malformed items JSON and missing `product_id` in items. Writes summary to `storage/app/imports/scan_missing_productid.json`.
- `scripts/fill_missing_userid.php` — fills/ensures `user_id` column exists in merged CSV and populates missing values with a default.
- `scripts/import_xlsx_and_dryrun.ps1` — PowerShell helper to export Excel sheets (sales/items/payments) to CSV templates and run the merge+dry-run sequence.

Importer command and fixes applied:
- Command: `php artisan import:sales <merged.csv>` with `--dry-run` option supported for validation before writing to DB.
- Invoice generation: importer calls `\App\Services\InvoiceGenerator::next('sale', $locationId)` for rows where `invoice_no` is empty (dry-run simulates; real run writes actual counters).
- Explicit IDs: If the CSV contains an explicit `id` (sale, sale_item, payment), the importer preserves and inserts that `id` value when present.
- Timestamp preservation: importer now preserves provided `created_at` and `updated_at` values if they are valid datetimes. Empty or invalid timestamp strings are normalized (parsed via Carbon) and fall back to the sale `date` to avoid inserting empty strings into DATETIME/TIMESTAMP columns (which caused DB errors during initial runs).
- Status normalization: importer normalizes `status` values to the allowed DB enum values `draft`, `posted`, `void` (case-insensitive). Any invalid or missing status falls back to `draft` to prevent "Data truncated for column 'status'" warnings.
- Additional datetime parsing: `posted_at` and `voided_at` fields are parsed and saved as proper datetimes or `NULL` when empty/invalid.

Validation & run summary from session:
- Merged CSV created and scanned: `tot_sales: 2806`, `malformed_items_json: 0`, `items_missing_product_id: 0`.
- Dry-run: `php artisan import:sales --dry-run "<merged.csv>"` — completed with Rows: 2806, Imported: 2806, Errors: 0.
- First real import attempt failed due to empty `created_at` values being inserted (DB rejected empty string for DATETIME). Importer was patched to normalize timestamps.
- Second real import produced enum truncation warnings for `status`. Importer was patched to normalize `status` values.
- Final real import (after fixes): completed successfully: Rows: 2806, Imported: 2806, Skipped: 0, Errors: 0. Invoice numbers were generated for rows that lacked `invoice_no`.

Post-import recommendations:
- Run verification queries to confirm counts and spot-check sample rows (verify explicit `id`/`created_at` preservation, invoice numbers format and uniqueness, and related `sale_items`/`sales_payments` counts).
- Keep the helper scripts in `scripts/` and the scanner output if you want an easy repeatable import flow. If they are no longer needed, they can be safely removed — they are convenience/temporary helpers.

---

Document prepared on 2025-09-17 (session notes appended).
