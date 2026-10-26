<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * This migration uses a raw ALTER TABLE statement so it works without
     * requiring the doctrine/dbal package (which is needed for the Schema
     * change() method). It sets a default empty string for `shop_name`.
     */
    public function up(): void
    {
        // Only run if the table exists
        if (Schema::hasTable('shops')) {
            try {
                DB::statement("ALTER TABLE `shops` MODIFY `shop_name` VARCHAR(150) NOT NULL DEFAULT ''");
            } catch (\Exception $e) {
                // Swallow errors - migration can be re-run after inspection
            }
        }
    }

    /**
     * Reverse the migrations.
     *
     * This removes the default on `shop_name` (leaving it NOT NULL). If you
     * previously had the column nullable, adapt this down() accordingly.
     */
    public function down(): void
    {
        $hasTable = DB::select("SHOW TABLES LIKE 'shops'");
        if ($hasTable) {
            try {
                DB::statement("ALTER TABLE `shops` MODIFY `shop_name` VARCHAR(150) NOT NULL");
            } catch (\Exception $e) {
                // no-op
            }
        }
    }
};
