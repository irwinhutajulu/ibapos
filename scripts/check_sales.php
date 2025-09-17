<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$status = $kernel->handle(
    $input = new Symfony\Component\Console\Input\ArgvInput([]),
    new Symfony\Component\Console\Output\ConsoleOutput()
);

// now we can use DB
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

$loc = 12;
$days = 90;

// Summary counts by status
$total = DB::table('sales')->where('location_id', $loc)->where('created_at','>=', Carbon::now()->subDays($days))->count();
echo "Total sales for location {$loc} in last {$days} days (all statuses): {$total}\n";

$byStatus = DB::table('sales')
    ->select('status', DB::raw('count(*) as cnt'))
    ->where('location_id', $loc)
    ->where('created_at','>=', Carbon::now()->subDays($days))
    ->groupBy('status')
    ->get();
echo "Counts by status:\n";
foreach($byStatus as $b) { echo "  {$b->status}: {$b->cnt}\n"; }

// Recent sales (last 20)
$rows = DB::table('sales')->where('location_id', $loc)->orderByDesc('posted_at')->take(20)->get(['id','invoice_no','status','total','posted_at','created_at']);
echo "\nLast up to 20 sales for location {$loc}:\n";
foreach($rows as $r) {
    echo "#{$r->id} invoice={$r->invoice_no} status={$r->status} total={$r->total} posted_at={$r->posted_at} created_at={$r->created_at}\n";
    $items = DB::table('sale_items')->where('sale_id', $r->id)->select(DB::raw('SUM(qty) as qty_sum'), DB::raw('SUM(subtotal) as subtotal_sum'))->first();
    echo "    items: qty_sum={$items->qty_sum} subtotal_sum={$items->subtotal_sum}\n";
}

// If nothing posted, show any sales with non-null totals
$nonZero = DB::table('sales')->where('location_id', $loc)->where('total','>',0)->orderByDesc('created_at')->take(10)->get(['id','invoice_no','status','total','posted_at']);
echo "\nSample non-zero total sales (if any):\n";
foreach($nonZero as $r) { echo "#{$r->id} invoice={$r->invoice_no} status={$r->status} total={$r->total} posted_at={$r->posted_at}\n"; }
$kernel->terminate($input, $status);
