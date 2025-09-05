<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('purchases', function (Blueprint $table) {
            $table->id();
            $table->string('invoice_no', 50);
            $table->dateTime('date');
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('location_id');
            $table->unsignedBigInteger('supplier_id');
            $table->decimal('total', 18, 2)->unsigned()->default(0);
            $table->decimal('total_weight', 18, 3)->unsigned()->default(0);
            $table->decimal('freight_cost', 18, 2)->unsigned()->default(0);
            $table->enum('status', ['draft','received','posted','void'])->default('draft');
            $table->dateTime('received_at')->nullable();
            $table->unsignedBigInteger('received_by')->nullable();
            $table->dateTime('posted_at')->nullable();
            $table->unsignedBigInteger('posted_by')->nullable();
            $table->dateTime('voided_at')->nullable();
            $table->unsignedBigInteger('voided_by')->nullable();
            $table->timestamps();

            $table->unique(['location_id', 'invoice_no']);
            $table->index(['date']);
            $table->foreign('user_id')->references('id')->on('users')->restrictOnDelete();
            $table->foreign('location_id')->references('id')->on('locations')->restrictOnDelete();
            $table->foreign('supplier_id')->references('id')->on('suppliers')->restrictOnDelete();
            $table->foreign('received_by')->references('id')->on('users')->nullOnDelete();
            $table->foreign('posted_by')->references('id')->on('users')->nullOnDelete();
            $table->foreign('voided_by')->references('id')->on('users')->nullOnDelete();
        });

        Schema::create('purchase_items', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('purchase_id');
            $table->unsignedBigInteger('product_id');
            $table->decimal('qty', 18, 3)->unsigned();
            $table->decimal('price', 18, 2)->unsigned();
            $table->decimal('subtotal', 18, 2)->unsigned();
            $table->timestamps();

            $table->index('purchase_id');
            $table->index('product_id');
            $table->foreign('purchase_id')->references('id')->on('purchases')->cascadeOnDelete();
            $table->foreign('product_id')->references('id')->on('products')->restrictOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('purchase_items');
        Schema::dropIfExists('purchases');
    }
};
