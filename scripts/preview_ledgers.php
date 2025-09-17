<?php
/**
 * Generate a preview of ledger rows that would be created when converting draft sales to posted.
 * This script is read-only and will not modify the DB. It prints the first N ledger rows.
 * Usage:
 *  php preview_ledgers.php --location=12 --limit=50
 */
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->handle($input = new Symfony\Component\Console\Input\ArgvInput([]), new Symfony\Component\Console\Output\ConsoleOutput());

use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

// parse args
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
$previewLimit = isset($options['limit']) && is_numeric($options['limit']) ? (int)$options['limit'] : 50;

echo "Preview ledger rows for location={$location} (limit={$previewLimit})\n";

// load draft sales
$sales = DB::table('sales')->where('location_id', $location)->where('status', 'draft')->orderBy('id')->get(['id','invoice_no','created_at']);

$previewRows = [];
foreach ($sales as $s) {
    $items = DB::table('sale_items')->where('sale_id', $s->id)->get(['id','product_id','qty','price','subtotal']);
    foreach ($items as $it) {
        // simulate ledger row
        $stockRow = DB::table('stocks')->where('product_id', $it->product_id)->where('location_id', $location)->first();
        $currentQty = $stockRow ? (float)$stockRow->qty : 0.0;
        $avgCost = $stockRow ? (float)$stockRow->avg_cost : null;
        $qtyDelta = -1.0 * (float)$it->qty;
        $newQty = $currentQty + $qtyDelta;
        $ledger = [
            'product_id' => $it->product_id,
            'location_id' => $location,
            'change' => $qtyDelta,
            'before_qty' => $currentQty,
            'after_qty' => $newQty,
            'unit_cost' => $avgCost,
            'ref_type' => 'sale_posting',
            'ref_id' => $s->id,
            'note' => "Posted from sale {$s->invoice_no}",
            'created_at' => Carbon::now()->toDateTimeString()
        ];
        $previewRows[] = $ledger;
        if (count($previewRows) >= $previewLimit) break 2;
    }
}

// print rows
$idx = 1;
foreach ($previewRows as $r) {
    echo "#{$idx} product_id={$r['product_id']} location_id={$r['location_id']} change={$r['change']} before={$r['before_qty']} after={$r['after_qty']} unit_cost=" . ($r['unit_cost'] === null ? 'NULL' : number_format($r['unit_cost'],2)) . " ref=sale:{$r['ref_id']} note=\"{$r['note']}\" created_at={$r['created_at']}\n";
    $idx++;
}

echo "Printed " . count($previewRows) . " preview ledger rows.\n";
$kernel->terminate($input, 0);
