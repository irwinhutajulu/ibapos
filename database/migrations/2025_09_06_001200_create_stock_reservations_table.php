<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('stock_reservations', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('product_id');
            $table->unsignedBigInteger('location_id');
            $table->unsignedBigInteger('sale_id');
            $table->unsignedBigInteger('sale_item_id')->nullable();
            $table->decimal('qty_reserved', 18, 3)->unsigned();
            $table->enum('status', ['active','consumed','released','expired'])->default('active');
            $table->dateTime('expires_at')->nullable();
            $table->unsignedBigInteger('created_by');
            $table->dateTime('released_at')->nullable();
            $table->unsignedBigInteger('released_by')->nullable();
            $table->dateTime('consumed_at')->nullable();
            $table->unsignedBigInteger('consumed_by')->nullable();
            $table->timestamp('created_at')->useCurrent();

            $table->index(['product_id','location_id']);
            $table->index(['sale_id','sale_item_id']);
            $table->foreign('product_id')->references('id')->on('products')->restrictOnDelete();
            $table->foreign('location_id')->references('id')->on('locations')->restrictOnDelete();
            $table->foreign('sale_id')->references('id')->on('sales')->cascadeOnDelete();
            $table->foreign('sale_item_id')->references('id')->on('sale_items')->nullOnDelete();
            $table->foreign('created_by')->references('id')->on('users')->restrictOnDelete();
            $table->foreign('released_by')->references('id')->on('users')->nullOnDelete();
            $table->foreign('consumed_by')->references('id')->on('users')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('stock_reservations');
    }
};
