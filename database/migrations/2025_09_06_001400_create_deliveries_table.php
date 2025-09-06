<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('deliveries', function (Blueprint $table) {
            $table->id();
            $table->string('code', 50)->unique();
            $table->dateTime('date');
            $table->unsignedBigInteger('location_id');
            $table->unsignedBigInteger('sale_id')->nullable();
            $table->enum('status', ['pending','assigned','in_transit','delivered','cancelled'])->default('pending');
            $table->unsignedBigInteger('assigned_to')->nullable();
            $table->dateTime('assigned_at')->nullable();
            $table->dateTime('delivered_at')->nullable();
            $table->string('note', 255)->nullable();
            $table->timestamps();

            $table->index(['date','status']);
            $table->foreign('location_id')->references('id')->on('locations')->restrictOnDelete();
            $table->foreign('sale_id')->references('id')->on('sales')->nullOnDelete();
            $table->foreign('assigned_to')->references('id')->on('users')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('deliveries');
    }
};
