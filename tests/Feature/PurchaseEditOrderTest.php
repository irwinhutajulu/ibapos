<?php

namespace Tests\Feature;

use App\Models\Product;
use App\Models\Purchase;
use App\Models\PurchaseItem;
use App\Models\Supplier;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PurchaseEditOrderTest extends TestCase
{
    use RefreshDatabase;

    public function test_editing_purchase_keeps_intended_item_order()
    {
    // seed products and a user
    $user = User::factory()->create();
    // ensure permissions exist and assign to user for update/show routes used in this test
    \Spatie\Permission\Models\Permission::firstOrCreate(['name' => 'purchases.update']);
    \Spatie\Permission\Models\Permission::firstOrCreate(['name' => 'purchases.read']);
    $user->givePermissionTo(['purchases.update','purchases.read']);
    $this->actingAs($user);

        $supplier = Supplier::factory()->create();
        $products = Product::factory()->count(3)->create();

        // create purchase with 3 items in order p1,p2,p3
    $purchase = Purchase::factory()->create(['supplier_id' => $supplier->id, 'user_id' => $user->id, 'status' => 'draft']);

        $items = [];
        foreach ($products as $p) {
            $items[] = PurchaseItem::create([
                'purchase_id' => $purchase->id,
                'product_id' => $p->id,
                'qty' => 1,
                'price' => 10,
                'subtotal' => 10,
            ]);
        }

        // Prepare payload: reorder items so last becomes second-last: p1, p3, p2
        $payload = [
            'invoice_no' => $purchase->invoice_no,
            'date' => $purchase->date->format('Y-m-d H:i:s'),
            'supplier_id' => $supplier->id,
            'freight_cost' => 0,
            'items' => [
                ['product_id' => $products[0]->id, 'qty' => 1, 'price' => 10],
                ['product_id' => $products[2]->id, 'qty' => 1, 'price' => 10],
                ['product_id' => $products[1]->id, 'qty' => 1, 'price' => 10],
            ],
        ];

        $response = $this->followingRedirects()->put(route('purchases.update', $purchase), $payload);
        $response->assertStatus(200);

        // reload items from DB ordered by id (insert order)
        $fresh = $purchase->fresh('items');
        $orderedProductIds = $fresh->items->pluck('product_id')->values()->all();

        $this->assertEquals([
            $products[0]->id,
            $products[2]->id,
            $products[1]->id,
        ], $orderedProductIds);
    }
}
