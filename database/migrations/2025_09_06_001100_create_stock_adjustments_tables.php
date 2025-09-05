<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('stock_adjustments', function (Blueprint $table) {
            $table->id();
            $table->string('code', 30)->nullable();
            $table->dateTime('date');
            $table->unsignedBigInteger('location_id');
            $table->unsignedBigInteger('user_id');
            $table->string('reason', 50)->nullable();
            $table->string('note', 255)->nullable();
            $table->enum('status', ['draft','posted','void'])->default('draft');
            $table->dateTime('posted_at')->nullable();
            $table->unsignedBigInteger('posted_by')->nullable();
            $table->dateTime('voided_at')->nullable();
            $table->unsignedBigInteger('voided_by')->nullable();
            $table->timestamps();

            $table->unique(['location_id','code']);
            $table->index(['location_id']);
            $table->index(['user_id']);
            $table->index(['status']);
            $table->foreign('location_id')->references('id')->on('locations')->restrictOnDelete();
            $table->foreign('user_id')->references('id')->on('users')->restrictOnDelete();
            $table->foreign('posted_by')->references('id')->on('users')->nullOnDelete();
            $table->foreign('voided_by')->references('id')->on('users')->nullOnDelete();
        });

        Schema::create('stock_adjustment_items', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('stock_adjustment_id');
            $table->unsignedBigInteger('product_id');
            $table->decimal('qty_change', 18, 3);
            $table->decimal('unit_cost', 18, 4)->unsigned()->nullable();
            $table->string('note', 255)->nullable();

            $table->index('stock_adjustment_id');
            $table->index('product_id');
            $table->foreign('stock_adjustment_id')->references('id')->on('stock_adjustments')->cascadeOnDelete();
            $table->foreign('product_id')->references('id')->on('products')->restrictOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('stock_adjustment_items');
        Schema::dropIfExists('stock_adjustments');
    }
};
