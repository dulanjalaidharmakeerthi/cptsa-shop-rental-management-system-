<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Adds `shop_name` column to `shops` table.
     */
    public function up(): void
    {
        if (Schema::hasTable('shops') && !Schema::hasColumn('shops', 'shop_name')) {
            Schema::table('shops', function (Blueprint $table) {
                $table->string('shop_name', 150)->after('shop_number')->default('');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('shops') && Schema::hasColumn('shops', 'shop_name')) {
            Schema::table('shops', function (Blueprint $table) {
                $table->dropColumn('shop_name');
            });
        }
    }
};
