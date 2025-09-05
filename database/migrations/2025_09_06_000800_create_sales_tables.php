<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('sales', function (Blueprint $table) {
            $table->id();
            $table->string('invoice_no', 50);
            $table->dateTime('date');
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('location_id');
            $table->unsignedBigInteger('customer_id')->nullable();
            $table->decimal('additional_fee', 18, 2)->unsigned()->default(0);
            $table->decimal('discount', 18, 2)->unsigned()->default(0);
            $table->decimal('total', 18, 2)->unsigned()->default(0);
            $table->decimal('payment', 18, 2)->unsigned()->default(0);
            $table->decimal('change', 18, 2)->unsigned()->default(0);
            $table->string('payment_type', 30)->nullable();
            $table->enum('status', ['draft','posted','void'])->default('draft');
            $table->dateTime('posted_at')->nullable();
            $table->unsignedBigInteger('posted_by')->nullable();
            $table->dateTime('voided_at')->nullable();
            $table->unsignedBigInteger('voided_by')->nullable();
            $table->timestamps();

            $table->unique(['location_id', 'invoice_no']);
            $table->index(['date']);
            $table->foreign('user_id')->references('id')->on('users')->restrictOnDelete();
            $table->foreign('location_id')->references('id')->on('locations')->restrictOnDelete();
            $table->foreign('customer_id')->references('id')->on('customers')->nullOnDelete();
            $table->foreign('posted_by')->references('id')->on('users')->nullOnDelete();
            $table->foreign('voided_by')->references('id')->on('users')->nullOnDelete();
        });

        Schema::create('sale_items', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('sale_id');
            $table->unsignedBigInteger('product_id');
            $table->decimal('qty', 18, 3)->unsigned();
            $table->decimal('price', 18, 2)->unsigned();
            $table->decimal('discount', 18, 2)->unsigned()->default(0);
            $table->decimal('subtotal', 18, 2)->unsigned();
            $table->unsignedBigInteger('source_location_id')->nullable();
            $table->timestamps();

            $table->index('sale_id');
            $table->index('product_id');
            $table->index('source_location_id');
            $table->foreign('sale_id')->references('id')->on('sales')->cascadeOnDelete();
            $table->foreign('product_id')->references('id')->on('products')->restrictOnDelete();
            $table->foreign('source_location_id')->references('id')->on('locations')->nullOnDelete();
        });

        Schema::create('sales_payments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('sale_id');
            $table->string('type', 30);
            $table->decimal('amount', 18, 2)->unsigned();
            $table->string('reference', 100)->nullable();
            $table->string('note', 255)->nullable();
            $table->dateTime('paid_at')->useCurrent();
            $table->timestamps();

            $table->index('sale_id');
            $table->index(['type']);
            $table->index(['paid_at']);
            $table->foreign('sale_id')->references('id')->on('sales')->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sales_payments');
        Schema::dropIfExists('sale_items');
        Schema::dropIfExists('sales');
    }
};
