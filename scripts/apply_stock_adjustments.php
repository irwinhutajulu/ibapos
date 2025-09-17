<?php
/**
 * Apply stock adjustments from CSV by calling InventoryService::adjustStockWithLedger
 * Usage:
 *   php apply_stock_adjustments.php --file=scripts/adjustments_location_12.csv [--apply] [--user=1]
 * By default it's a dry-run (no DB writes). Use --apply to perform changes.
 */
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->handle($input = new Symfony\Component\Console\Input\ArgvInput([]), new Symfony\Component\Console\Output\ConsoleOutput());

use Illuminate\Support\Facades\DB;
use App\Services\InventoryService;

$options = [];
foreach ($argv as $arg) {
    if (strpos($arg, '--') === 0) {
        $parts = explode('=', ltrim($arg, '-'), 2);
        $k = $parts[0];
        $v = $parts[1] ?? true;
        $options[$k] = $v;
    }
}
if (empty($options['file'])) {
    echo "Error: --file=path required\n";
    exit(1);
}
$file = $options['file'];
$apply = isset($options['apply']);
$userId = isset($options['user']) ? (int)$options['user'] : 1;
// validate user exists; if not, use null so FK constraint isn't violated
if ($userId > 0) {
    $userExists = DB::table('users')->where('id', $userId)->exists();
    if (! $userExists) {
        echo "Warning: user id $userId not found in users table; ledger entries will record NULL user_id.\n";
        $userId = null;
    }
} else {
    $userId = null;
}

if (!file_exists($file)) {
    echo "Error: file not found: $file\n";
    exit(1);
}

$fp = fopen($file, 'r');
$header = fgetcsv($fp);
$colIndex = array_flip($header ?: []);

$totalLines = 0;
$toApply = 0;
$totalQty = 0.0;
$errors = 0;

echo ($apply ? "Running in APPLY mode (DB changes will be made)\n" : "Dry-run: no DB changes. Use --apply to perform updates.\n");

while (($row = fgetcsv($fp)) !== false) {
    $totalLines++;
    $product_id = isset($colIndex['product_id']) ? (int)$row[$colIndex['product_id']] : (int)$row[0];
    $location_id = isset($colIndex['location_id']) ? (int)$row[$colIndex['location_id']] : (int)$row[1];
    $adjust_qty = isset($colIndex['adjust_qty']) ? (float)$row[$colIndex['adjust_qty']] : (float)$row[2];
    $unit_cost = isset($colIndex['unit_cost']) ? $row[$colIndex['unit_cost']] : null;
    // normalize empty unit_cost ("") coming from CSV to null so DB decimal columns don't get ''
    if (is_string($unit_cost) && trim($unit_cost) === '') {
        $unit_cost = null;
    }
    $reason = isset($colIndex['reason']) ? $row[$colIndex['reason']] : 'Imported adjustment';

    if ($adjust_qty <= 0) {
        continue; // skip zero adjustments
    }

    $toApply++;
    $totalQty += $adjust_qty;

    // fetch current stock row for info
    $stock = DB::table('stocks')->where('product_id', $product_id)->where('location_id', $location_id)->first();
    $before = $stock ? (float)$stock->qty : 0.0;
    $after = $before + $adjust_qty;

    $msg = sprintf("#%d product=%d location=%d adjust=+%s before=%s after=%s unit_cost=%s reason=%s\n",
        $totalLines, $product_id, $location_id, rtrim(rtrim(number_format($adjust_qty,4,'.',''), '0'), '.'), rtrim(rtrim(number_format($before,4,'.',''), '0'), '.'), rtrim(rtrim(number_format($after,4,'.',''), '0'), '.'), $unit_cost === null ? 'NULL' : $unit_cost, $reason
    );

    echo $msg;

    if ($apply) {
        try {
            DB::beginTransaction();
            $inv = new InventoryService();
            // ref_type = stock_adjustment, ref_id = 0 (no specific ref id), pass userId and reason
            // InventoryService expects qtyDelta as string and refId as int
            $inv->adjustStockWithLedger($product_id, $location_id, (string)$adjust_qty, $unit_cost, 'stock_adjustment', 0, $userId, $reason);
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            $errors++;
            echo "  ERROR applying adjustment: " . $e->getMessage() . "\n";
        }
    }
}

fclose($fp);

echo "\nProcessed lines: $totalLines, adjustments (non-zero): $toApply, total qty: $totalQty, errors: $errors\n";

$kernel->terminate($input, 0);
