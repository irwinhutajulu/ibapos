<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('kasbons', function (Blueprint $table) {
            $table->id();
            $table->string('code', 50)->unique();
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('location_id');
            $table->date('date');
            $table->decimal('amount', 18, 2)->unsigned();
            $table->enum('status', ['requested','approved','rejected','settled'])->default('requested');
            $table->unsignedBigInteger('approved_by')->nullable();
            $table->dateTime('approved_at')->nullable();
            $table->string('note', 255)->nullable();
            $table->timestamps();

            $table->index(['date','status']);
            $table->foreign('user_id')->references('id')->on('users')->restrictOnDelete();
            $table->foreign('location_id')->references('id')->on('locations')->restrictOnDelete();
            $table->foreign('approved_by')->references('id')->on('users')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('kasbons');
    }
};
