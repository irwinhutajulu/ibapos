<?php

namespace Tests\Unit;

use App\Http\Controllers\StockController;
use PHPUnit\Framework\TestCase;

class StockControllerFormatterTest extends TestCase
{
    public function test_format_stocks_for_live_search_returns_expected_shape()
    {
        // Provide a simple url() helper for the test runtime
        if (!function_exists('url')) {
            function url($path = '') {
                return 'http://localhost/'.ltrim($path, '/');
            }
        }

        $stocks = [
            (object)[ 'id' => 1, 'product_id' => 11, 'qty' => '5', 'avg_cost' => '1000', 'product' => (object)['id'=>11,'name'=>'P1','barcode'=>'123','image_path'=>null] ],
        ];

        $result = StockController::formatStocksForLiveSearch($stocks, ['total'=>1,'per_page'=>20,'current_page'=>1,'last_page'=>1,'from'=>1,'to'=>1]);

        $this->assertIsArray($result);
        $this->assertTrue($result['success']);
        $this->assertCount(1, $result['data']);
        $row = $result['data'][0];
        $this->assertArrayHasKey('id', $row);
        $this->assertArrayHasKey('cells', $row);
        $this->assertArrayHasKey('actions', $row);
    }
}
