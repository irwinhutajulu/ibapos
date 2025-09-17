<?php
/**
 * Convert draft sales to posted sales safely.
 * Usage (dry-run, safe):
 *   php convert_sales_to_posted.php --location=12 --limit=100
 * To apply changes:
 *   php convert_sales_to_posted.php --location=12 --apply --limit=100
 * To also decrement stock (may cause negative stock if inventory insufficient):
 *   php convert_sales_to_posted.php --location=12 --apply --adjust-stock
 * Flags:
 *   --location=ID   (required)
 *   --limit=N        (optional, default all)
 *   --apply          (optional, if present script will modify DB)
 *   --adjust-stock   (optional, only valid with --apply)
 *   --force          (bypass safety checks like negative stock warnings)
 *
 * This script is idempotent for sales already status='posted' -- they are skipped.
 * It logs actions and will ask nothing interactively.
 */

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$status = $kernel->handle(
    $input = new Symfony\Component\Console\Input\ArgvInput([]),
    new Symfony\Component\Console\Output\ConsoleOutput()
);

use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
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

if (empty($options['location'])) {
    echo "Error: --location=ID required\n";
    exit(1);
}
$location = (int)$options['location'];
$limit = isset($options['limit']) && is_numeric($options['limit']) ? (int)$options['limit'] : null;
$apply = isset($options['apply']);
$adjustStock = isset($options['adjust-stock']);
$force = isset($options['force']);

echo "Convert draft sales to posted for location={$location}\n";
if ($apply) echo "Mode: APPLY (will modify DB)\n"; else echo "Mode: DRY-RUN (no DB changes)\n";
if ($adjustStock) echo "Stock adjustment requested. (Only with --apply)\n";

$query = DB::table('sales')->where('location_id', $location)->where('status', 'draft');
if ($limit) $query->limit($limit);
$sales = $query->get(['id','invoice_no','total','created_at']);

if ($sales->isEmpty()) {
    echo "No draft sales found for location {$location}\n";
    exit(0);
}

// Gather stock impact
$skuImpacts = [];
$issues = [];
foreach ($sales as $s) {
    $items = DB::table('sale_items')->where('sale_id', $s->id)->get(['product_id','qty']);
    foreach ($items as $it) {
        $skuImpacts[$it->product_id] = ($skuImpacts[$it->product_id] ?? 0) + $it->qty;
    }
}

// Check current stock
$stockShortages = [];
if ($adjustStock) {
    foreach ($skuImpacts as $product_id => $qtyNeeded) {
        $stockRow = DB::table('stocks')->where('product_id', $product_id)->where('location_id', $location)->first();
        $current = $stockRow ? (float)$stockRow->qty : 0.0;
        if ($current < $qtyNeeded) {
            $stockShortages[$product_id] = ['current' => $current, 'needed' => $qtyNeeded];
        }
    }
}

// Report summary
echo "Found {$sales->count()} draft sales. Unique products impacted: " . count($skuImpacts) . "\n";
if ($adjustStock) {
    if (!empty($stockShortages)) {
        echo "Stock shortages detected for the following products:\n";
        foreach ($stockShortages as $pid => $meta) {
            echo "  product_id={$pid} current={$meta['current']} needed={$meta['needed']}\n";
        }
        if (!$force) {
            echo "Aborting because stock shortages exist (use --force to proceed).\n";
            exit(2);
        } else {
            echo "--force provided: continuing despite shortages (may create negative stock).\n";
        }
    } else {
        echo "Stock check OK: all products have sufficient qty.\n";
    }
}

// Perform (or simulate) conversion
$converted = 0;
foreach ($sales as $s) {
    echo "Sale #{$s->id} invoice={$s->invoice_no} => will set status=posted posted_at=now\n";
        if ($apply) {
        DB::table('sales')->where('id', $s->id)->update(['status' => 'posted', 'posted_at' => Carbon::now(), 'updated_at' => Carbon::now()]);
        $converted++;
        if ($adjustStock) {
            // Use InventoryService to create ledger rows and emit events
            $inventory = $app->make(InventoryService::class);
            $items = DB::table('sale_items')->where('sale_id', $s->id)->get(['product_id','qty']);
            foreach ($items as $it) {
                // qtyDelta must be negative for an outgoing sale
                $qtyDelta = (string)(-1 * (float)$it->qty);
                // try to fetch current avg cost if available
                $stockRow = DB::table('stocks')->where('product_id', $it->product_id)->where('location_id', $location)->first();
                $costPerUnit = $stockRow ? (string)$stockRow->avg_cost : null;

                // Call the service (this will create ledger entries and emit StockUpdated event)
                $inventory->adjustStockWithLedger(
                    productId: (int)$it->product_id,
                    locationId: $location,
                    qtyDelta: $qtyDelta,
                    costPerUnit: $costPerUnit,
                    refType: 'sale_posting',
                    refId: $s->id,
                    userId: null,
                    note: 'Posted from batch convert_sales_to_posted'
                );
            }
        }
    }
}

echo "Summary: converted={$converted} (if --apply was provided)\n";
$kernel->terminate($input, $status);
