<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('stock_ledger', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('product_id');
            $table->unsignedBigInteger('location_id');
            $table->string('ref_type', 50);
            $table->unsignedBigInteger('ref_id');
            $table->decimal('qty_change', 18, 3);
            $table->decimal('balance_after', 18, 3)->unsigned();
            $table->decimal('cost_per_unit_at_time', 18, 4)->unsigned()->nullable();
            $table->decimal('total_cost_effect', 18, 2)->nullable();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->string('note', 255)->nullable();
            $table->timestamp('created_at')->useCurrent();

            $table->index(['product_id','location_id']);
            $table->index(['ref_type','ref_id']);
            $table->index(['created_at']);
            $table->foreign('product_id')->references('id')->on('products')->restrictOnDelete();
            $table->foreign('location_id')->references('id')->on('locations')->restrictOnDelete();
            $table->foreign('user_id')->references('id')->on('users')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('stock_ledger');
    }
};
