<?php

namespace Database\Seeders;

use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\Customer;
use App\Models\Product;
use App\Models\Location;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SalesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get required data
        $users = User::all();
        $locations = Location::all();
        $customers = Customer::all();
        $products = Product::all();

        if ($users->isEmpty() || $locations->isEmpty() || $products->isEmpty()) {
            $this->command->warn('Please run UserSeeder, LocationSeeder, and ProductSeeder first!');
            return;
        }

        // Create sample sales
        for ($i = 1; $i <= 15; $i++) {
            $user = $users->random();
            $location = $locations->random();
            $customer = $customers->isNotEmpty() ? $customers->random() : null;
            
            // Create unique invoice number per location
            $existingCount = Sale::where('location_id', $location->id)->count();
            $invoiceNumber = 'INV-' . $location->id . '-' . str_pad($existingCount + 1, 6, '0', STR_PAD_LEFT);
            
            $sale = Sale::create([
                'invoice_no' => $invoiceNumber,
                'date' => now()->subDays(rand(0, 30)),
                'user_id' => $user->id,
                'location_id' => $location->id,
                'customer_id' => $customer?->id,
                'additional_fee' => rand(0, 1) ? rand(5000, 25000) : 0,
                'discount' => rand(0, 1) ? rand(10000, 50000) : 0,
                'total' => 0, // Will be calculated after adding items
                'payment' => 0, // Will be set after items
                'change' => 0,
                'payment_type' => ['cash', 'debit', 'transfer', 'e-wallet'][rand(0, 3)],
                'status' => ['draft', 'posted', 'void'][rand(0, 2)],
            ]);

            // Add random sale items
            $itemCount = rand(1, 5);
            $totalAmount = 0;

            for ($j = 0; $j < $itemCount; $j++) {
                $product = $products->random();
                $qty = rand(1, 5);
                $price = max(1000, $product->price + rand(-5000, 10000)); // Ensure minimum price
                $discount = rand(0, 1) ? min($price * 0.5, rand(1000, 5000)) : 0; // Discount max 50% of price
                $subtotal = max(0, ($price - $discount) * $qty); // Ensure positive subtotal
                $totalAmount += $subtotal;

                SaleItem::create([
                    'sale_id' => $sale->id,
                    'product_id' => $product->id,
                    'qty' => $qty,
                    'price' => $price,
                    'discount' => $discount,
                    'subtotal' => $subtotal,
                    'source_location_id' => rand(0, 1) ? $location->id : null, // Sometimes use remote stock
                ]);
            }

            // Update sale totals
            $finalTotal = max(0, $totalAmount + $sale->additional_fee - $sale->discount);
            $payment = max($finalTotal, $finalTotal + rand(0, 10000)); // Always pay at least the total
            $change = $payment - $finalTotal;

            $sale->update([
                'total' => $finalTotal,
                'payment' => $payment,
                'change' => $change,
                'posted_at' => $sale->status === 'posted' ? $sale->date : null,
                'posted_by' => $sale->status === 'posted' ? $user->id : null,
                'voided_at' => $sale->status === 'void' ? $sale->date->addMinutes(30) : null,
                'voided_by' => $sale->status === 'void' ? $user->id : null,
            ]);
        }

        $this->command->info('Created 15 sample sales with items!');
    }
}
