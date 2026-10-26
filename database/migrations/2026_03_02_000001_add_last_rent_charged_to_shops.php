<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Add a date to shops so we know when monthly rent was last added to due_payment.
     * This enables the system to automatically credit the rent each 1st of the month
     * (or the first time someone views/payments for the shop after the month begins)
     * and prevents double‑charging.
     */
    public function up()
    {
        Schema::table('shops', function (Blueprint $table) {
            $table->date('last_rent_charged')->nullable()->after('due_interest');
        });
    }

    /**
     * Reverse the migration.
     */
    public function down()
    {
        Schema::table('shops', function (Blueprint $table) {
            $table->dropColumn('last_rent_charged');
        });
    }
};
