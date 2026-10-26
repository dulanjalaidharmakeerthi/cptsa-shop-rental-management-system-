<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * We'll add a small column to record the saving adjustment performed during
     * a payment transaction. This is useful for audit/history and makes it
     * possible to handle "only saving" adjustments (no money/interest).
     */
    public function up()
    {
        Schema::table('payments', function (Blueprint $table) {
            $table->decimal('saving_adjustment', 10, 2)->default(0.00)->after('interest');
        });

        Schema::table('payment_details', function (Blueprint $table) {
            $table->decimal('saving_adjustment', 10, 2)->default(0.00)->after('interest');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::table('payment_details', function (Blueprint $table) {
            $table->dropColumn('saving_adjustment');
        });
        Schema::table('payments', function (Blueprint $table) {
            $table->dropColumn('saving_adjustment');
        });
    }
};
