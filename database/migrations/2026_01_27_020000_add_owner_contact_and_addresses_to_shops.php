<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('shops', function (Blueprint $table) {
            if (!Schema::hasColumn('shops', 'shop_owner_phone')) {
                $table->string('shop_owner_phone', 40)->nullable()->after('shop_owner_name');
            }
            if (!Schema::hasColumn('shops', 'shop_owner_address')) {
                $table->string('shop_owner_address', 255)->nullable()->after('shop_owner_phone');
            }
            if (!Schema::hasColumn('shops', 'shop_address')) {
                $table->string('shop_address', 255)->nullable()->after('location');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('shops', function (Blueprint $table) {
            $drops = [];
            if (Schema::hasColumn('shops', 'shop_owner_phone')) {
                $drops[] = 'shop_owner_phone';
            }
            if (Schema::hasColumn('shops', 'shop_owner_address')) {
                $drops[] = 'shop_owner_address';
            }
            if (Schema::hasColumn('shops', 'shop_address')) {
                $drops[] = 'shop_address';
            }
            if (!empty($drops)) {
                $table->dropColumn($drops);
            }
        });
    }
};
