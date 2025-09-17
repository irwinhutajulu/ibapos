<?php

namespace Tests\Feature;

use App\Models\Location;
use App\Models\Product;
use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\Stock;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DashboardApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_reports_dashboard_returns_expected_shape()
    {
        $user = User::factory()->create();
        $loc = Location::factory()->create();
        $user->locations()->attach($loc->id);
        $this->actingAs($user);
        session(['active_location_id' => $loc->id]);

        // create product, stock and a posted sale
        $p = Product::factory()->create(['name' => 'Test Product']);
        Stock::create(['product_id' => $p->id, 'location_id' => $loc->id, 'qty' => 5, 'avg_cost' => 1000]);

        $sale = Sale::create(['invoice_no'=>'TST1','date'=>now(),'user_id'=>$user->id,'location_id'=>$loc->id,'status'=>'posted','total'=>15000,'posted_at'=>now()]);
    SaleItem::create(['sale_id'=>$sale->id,'product_id'=>$p->id,'qty'=>2,'price'=>7500, 'subtotal' => 2 * 7500]);

        $resp = $this->getJson('/api/reports/dashboard');
        $resp->assertStatus(200)->assertJsonStructure([
            'today_total','today_count','recent','top_product','stock_alerts','location_id'
        ]);

        $json = $resp->json();
        $this->assertEquals($loc->id, $json['location_id']);
        $this->assertGreaterThanOrEqual(15000, $json['today_total']);
        $this->assertGreaterThanOrEqual(1, $json['today_count']);
    }
}
