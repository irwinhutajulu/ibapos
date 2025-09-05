<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('stock_mutations', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('product_id');
            $table->unsignedBigInteger('from_location_id');
            $table->unsignedBigInteger('to_location_id');
            $table->decimal('qty', 18, 3)->unsigned();
            $table->date('date');
            $table->string('note', 255)->nullable();
            $table->enum('status', ['pending','confirmed','rejected'])->default('pending');
            $table->unsignedBigInteger('requested_by');
            $table->unsignedBigInteger('confirmed_by')->nullable();
            $table->dateTime('confirmed_at')->nullable();
            $table->timestamps();

            $table->index('product_id');
            $table->index('from_location_id');
            $table->index('to_location_id');
            $table->index('status');
            $table->foreign('product_id')->references('id')->on('products')->restrictOnDelete();
            $table->foreign('from_location_id')->references('id')->on('locations')->restrictOnDelete();
            $table->foreign('to_location_id')->references('id')->on('locations')->restrictOnDelete();
            $table->foreign('requested_by')->references('id')->on('users')->restrictOnDelete();
            $table->foreign('confirmed_by')->references('id')->on('users')->nullOnDelete();
        });

        // Add CHECK constraint if DB supports (MySQL 8.0.16+)
        try {
            DB::statement('ALTER TABLE stock_mutations ADD CONSTRAINT chk_locations_different CHECK (from_location_id <> to_location_id)');
        } catch (\Throwable $e) {
            // ignore if not supported
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('stock_mutations');
    }
};
