<?php

namespace Tests\Feature;

use App\Models\Location;
use App\Models\Product;
use App\Models\Stock;
use App\Models\User;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class StocksLiveSearchTest extends TestCase
{
    protected function setUp(): void
    {
        // Use in-memory sqlite for tests to ensure a clean schema and avoid
        // duplicate-column migration conflicts.
        putenv('DB_CONNECTION=sqlite');
        putenv('DB_DATABASE=:memory:');

        parent::setUp();

        // Ensure migrations table exists and mark the second duplicate migration as already run
        if (!Schema::hasTable('migrations')) {
            Schema::create('migrations', function ($table) {
                $table->increments('id');
                $table->string('migration');
                $table->integer('batch');
            });
        }

        // Insert a record for the later duplicate migration so Laravel will skip it when running migrate
        DB::table('migrations')->insertOrIgnore([
            'migration' => '2025_09_07_203957_add_image_path_to_products_table',
            'batch' => 1,
        ]);

        // Run migrations now (fresh in-memory DB)
        $this->artisan('migrate', ['--force' => true]);

        $this->seed(\Database\Seeders\PermissionsSeeder::class);
    }

    public function test_ajax_search_returns_expected_json_shape()
    {
        $loc = Location::factory()->create();
        $user = User::factory()->create();
        $user->assignRole('super-admin');
        $user->locations()->attach($loc->id);
        session(['active_location_id' => $loc->id]);

        $p = Product::factory()->create(['name' => 'Test Product', 'barcode' => '1234567890123']);
        Stock::create(['product_id' => $p->id, 'location_id' => $loc->id, 'qty' => '5', 'avg_cost' => '1000']);

        $this->actingAs($user)
             ->getJson(route('stocks.index', ['q' => 'Test']))
             ->assertStatus(200)
             ->assertJsonStructure([
                 'success',
                 'data' => [
                     ['id', 'cells', 'actions']
                 ],
                 'pagination' => ['total', 'per_page', 'current_page', 'last_page', 'from', 'to']
             ])
             ->assertJson(['success' => true]);
    }
}
