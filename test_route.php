<?php
// Simple test endpoint
Route::get('/test-api', function() {
    try {
        $products = \App\Models\Product::select('id', 'name')->limit(3)->get();
        return response()->json([
            'success' => true,
            'data' => $products,
            'message' => 'Test successful'
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'error' => $e->getMessage(),
            'trace' => $e->getTraceAsString()
        ], 500);
    }
});
