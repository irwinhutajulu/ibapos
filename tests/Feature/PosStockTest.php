<?php

namespace Tests\Feature;

use App\Models\Location;
use App\Models\Product;
use App\Models\Stock;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PosStockTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Create test user and location
        $this->user = User::factory()->create();
        $this->location = Location::factory()->create(['name' => 'Test Location']);
        $this->otherLocation = Location::factory()->create(['name' => 'Other Location']);
        
        // Assign user to location
        $this->user->locations()->attach($this->location->id);
        
        // Create test products
        $this->product1 = Product::factory()->create(['name' => 'Test Product 1', 'price' => 100000]);
        $this->product2 = Product::factory()->create(['name' => 'Test Product 2', 'price' => 200000]);
        
        // Create stock data
        Stock::create([
            'product_id' => $this->product1->id,
            'location_id' => $this->location->id,
            'qty' => 50,
            'avg_cost' => 80000
        ]);
        
        Stock::create([
            'product_id' => $this->product1->id,
            'location_id' => $this->otherLocation->id,
            'qty' => 30,
            'avg_cost' => 85000
        ]);
        
        Stock::create([
            'product_id' => $this->product2->id,
            'location_id' => $this->location->id,
            'qty' => 0, // Out of stock
            'avg_cost' => 150000
        ]);
    }

    public function test_product_api_returns_correct_stock_data()
    {
        $this->actingAs($this->user);
        
        // Set active location
        session(['active_location_id' => $this->location->id]);
        
        $response = $this->getJson('/api/products?q=Test');
        
        $response->assertOk();
        
        $data = $response->json();
        $this->assertIsArray($data);
        $this->assertCount(2, $data);
        
        // Check first product has stock data
        $product1Data = collect($data)->firstWhere('id', $this->product1->id);
        $this->assertNotNull($product1Data);
        $this->assertArrayHasKey('stocks', $product1Data);
        $this->assertNotEmpty($product1Data['stocks']);
        
        // Verify stock data structure
        $stock = $product1Data['stocks'][0];
        $this->assertArrayHasKey('qty', $stock);
        $this->assertArrayHasKey('location_id', $stock);
        $this->assertEquals('50.000', $stock['qty']); // Database returns as string with decimals
    }

    public function test_stock_api_available_returns_correct_values()
    {
        $this->actingAs($this->user);
        session(['active_location_id' => $this->location->id]);
        
        // Test product with stock
        $response = $this->getJson("/api/stock/available?product_id={$this->product1->id}&location_id={$this->location->id}");
        
        $response->assertOk();
        $data = $response->json();
        
        $this->assertArrayHasKey('available', $data);
        $this->assertArrayHasKey('on_hand', $data);
        $this->assertArrayHasKey('location_id', $data);
        $this->assertEquals(50, $data['available']);
        $this->assertEquals(50, $data['on_hand']);
        $this->assertEquals($this->location->id, $data['location_id']);
    }

    public function test_stock_api_available_batch_returns_correct_data()
    {
        $this->actingAs($this->user);
        session(['active_location_id' => $this->location->id]);
        
        $items = [
            ['product_id' => $this->product1->id, 'location_id' => $this->location->id],
            ['product_id' => $this->product2->id, 'location_id' => $this->location->id],
        ];
        
        $response = $this->postJson('/api/stock/available-batch', ['items' => $items]);
        
        $response->assertOk();
        $data = $response->json();
        
        $this->assertArrayHasKey('data', $data);
        $this->assertCount(2, $data['data']);
        
        // Check first product (has stock)
        $this->assertEquals(50, $data['data'][0]['available']);
        
        // Check second product (no stock)
        $this->assertEquals(0, $data['data'][1]['available']);
    }

    public function test_stock_api_handles_missing_active_location()
    {
        $this->actingAs($this->user);
        // Don't set active location
        
        $response = $this->getJson("/api/stock/available?product_id={$this->product1->id}");
        
        $response->assertStatus(400);
        $data = $response->json();
        $this->assertArrayHasKey('error', $data);
        $this->assertEquals(0, $data['available']);
    }

    public function test_pos_page_loads_successfully()
    {
        $this->actingAs($this->user);
        session(['active_location_id' => $this->location->id]);
        
        $response = $this->get('/pos');
        
        $response->assertOk();
        $response->assertViewIs('pos.index');
    }
}