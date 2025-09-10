<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        // Only add column if it doesn't already exist (prevents duplicate column errors in tests)
        if (!Schema::hasColumn('products', 'image_path')) {
            Schema::table('products', function (Blueprint $table) {
                $table->string('image_path', 255)->nullable()->after('unit');
            });
        }
    }

    public function down(): void
    {
        // Only drop if column exists
        if (Schema::hasColumn('products', 'image_path')) {
            Schema::table('products', function (Blueprint $table) {
                $table->dropColumn('image_path');
            });
        }
    }
};
