<?php

namespace Tests\Unit;

use App\Models\Purchase;
use App\Models\PurchaseItem;
use App\Models\Product;
use App\Models\Supplier;
use App\Models\Location;
use App\Services\PurchasePostingService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PurchasePostingServiceAllocationTest extends TestCase
{
    use RefreshDatabase;

    public function test_allocation_includes_loading_and_unloading_and_sums_to_total()
    {
        // seed minimal product/supplier/location
        $productA = Product::factory()->create(['price' => 10]);
        $productB = Product::factory()->create(['price' => 20]);
        $supplier = Supplier::factory()->create();
        $location = Location::factory()->create();
        $user = \App\Models\User::factory()->create();

        // set product weights so allocation is by weight
        $productA->weight = 2.0; // kg per unit
        $productA->save();
        $productB->weight = 1.0; // kg per unit
        $productB->save();

        // total weight will be computed as qty * product.weight (10*2 + 10*1 = 30)
        $purchase = Purchase::create([
            'invoice_no' => 'T-ALLOC-1',
            'date' => now(),
            'user_id' => $user->id,
            'location_id' => $location->id,
            'supplier_id' => $supplier->id,
            'total' => 300,
            'total_weight' => 30,
            'freight_cost' => 10.00,
            'loading_cost' => 5.00,
            'unloading_cost' => 3.00,
            'status' => 'draft'
        ]);

        // create items with subtotals 100 and 200 (proportional 1:2)
        $item1 = PurchaseItem::create(['purchase_id' => $purchase->id, 'product_id' => $productA->id, 'qty' => 10, 'price' => 10, 'subtotal' => 100]);
        $item2 = PurchaseItem::create(['purchase_id' => $purchase->id, 'product_id' => $productB->id, 'qty' => 10, 'price' => 20, 'subtotal' => 200]);

        $purchase->setRelation('items', collect([$item1, $item2]));

        $svc = new PurchasePostingService();
        $alloc = $svc->calculateLandedCost($purchase);

        $this->assertIsArray($alloc);
        $this->assertCount(2, $alloc);

        $totalExtra = 10.00 + 5.00 + 3.00; // 18.00
        $sum = array_sum(array_values($alloc));
        $this->assertEqualsWithDelta($totalExtra, $sum, 0.01, 'Allocations should sum to total extra cost');

    // weight proportions: item1 weight = 10*2 = 20; item2 weight = 10*1 = 10 -> proportions 2:1
    // so item1 should receive ~12 (2/3 of 18), item2 ~6 (1/3 of 18)
    $this->assertEqualsWithDelta(12.00, $alloc[$item1->id], 0.5);
    $this->assertEqualsWithDelta(6.00, $alloc[$item2->id], 0.5);
    }
}
