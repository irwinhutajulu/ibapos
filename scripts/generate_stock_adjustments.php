<?php
/**
 * Generate stock_adjustment input from draft sale_items.
 * Outputs CSV to stdout (product_id,location_id,adjustment_qty,reason,unit_cost)
 * Usage:
 *  php generate_stock_adjustments.php --location=12 --output=adjustments.csv
 */
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->handle($input = new Symfony\Component\Console\Input\ArgvInput([]), new Symfony\Component\Console\Output\ConsoleOutput());

use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

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
$outputFile = isset($options['output']) ? $options['output'] : null;

// Aggregate needed qty per product from draft sale_items
$rows = DB::table('sales')
    ->where('location_id', $location)
    ->where('status', 'draft')
    ->join('sale_items', 'sales.id', '=', 'sale_items.sale_id')
    ->selectRaw('sale_items.product_id, SUM(sale_items.qty) as needed_qty')
    ->groupBy('sale_items.product_id')
    ->get();

$adjustments = [];
foreach ($rows as $r) {
    $product_id = (int)$r->product_id;
    $needed = (float)$r->needed_qty;
    $stockRow = DB::table('stocks')->where('product_id', $product_id)->where('location_id', $location)->first();
    $current = $stockRow ? (float)$stockRow->qty : 0.0;
    $shortage = max(0, $needed - $current);
    $unit_cost = $stockRow ? $stockRow->avg_cost : null;

    $adjustments[] = [
        'product_id' => $product_id,
        'location_id' => $location,
        'needed_qty' => $needed,
        'current_qty' => $current,
        'shortage' => $shortage,
        'unit_cost' => $unit_cost
    ];
}

// Output CSV
$fp = $outputFile ? fopen($outputFile, 'w') : fopen('php://stdout', 'w');
// header: for stock adjustment import we might need product_id,location_id,adjust_qty,unit_cost,reason
fputcsv($fp, ['product_id','location_id','adjust_qty','unit_cost','reason']);
foreach ($adjustments as $a) {
    // We'll produce an adjustment that increases stock by `shortage` so posting won't go negative.
    $adjust_qty = $a['shortage'];
    $reason = 'Auto top-up for posting draft sales';
    fputcsv($fp, [$a['product_id'], $a['location_id'], $adjust_qty, $a['unit_cost'], $reason]);
}
if ($outputFile) fclose($fp);

echo "Wrote " . count($adjustments) . " adjustment lines." . PHP_EOL;
$kernel->terminate($input, 0);
