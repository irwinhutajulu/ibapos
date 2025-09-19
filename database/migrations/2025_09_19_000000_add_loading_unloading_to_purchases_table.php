<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('purchases', function (Blueprint $table) {
            // The table uses `freight_cost` as the existing freight column name.
            // Ensure we add the new columns after that column so the table order remains logical.
            if (!Schema::hasColumn('purchases', 'loading_cost')) {
                $table->decimal('loading_cost', 12, 2)->default(0)->after('freight_cost');
            }
            if (!Schema::hasColumn('purchases', 'unloading_cost')) {
                $table->decimal('unloading_cost', 12, 2)->default(0)->after('loading_cost');
            }
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('purchases', function (Blueprint $table) {
            if (Schema::hasColumn('purchases', 'unloading_cost')) {
                $table->dropColumn('unloading_cost');
            }
            if (Schema::hasColumn('purchases', 'loading_cost')) {
                $table->dropColumn('loading_cost');
            }
        });
    }
};
