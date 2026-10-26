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
        if (Schema::hasTable('shops') && !Schema::hasColumn('shops', 'bus_stand_id')) {
            Schema::table('shops', function (Blueprint $table) {
                $table->unsignedInteger('bus_stand_id')->nullable()->after('agreement_duration')->index();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('shops') && Schema::hasColumn('shops', 'bus_stand_id')) {
            Schema::table('shops', function (Blueprint $table) {
                $table->dropColumn('bus_stand_id');
            });
        }
    }
};
