<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('invoice_counters', function (Blueprint $table) {
            $table->id();
            $table->string('type', 50);
            $table->unsignedBigInteger('location_id')->nullable();
            $table->unsignedBigInteger('last_number')->default(0);
            $table->date('date')->nullable(); // optional: for daily reset
            $table->timestamps();

            $table->unique(['type', 'location_id', 'date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('invoice_counters');
    }
};
