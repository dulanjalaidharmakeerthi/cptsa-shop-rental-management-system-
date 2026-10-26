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
        if (!Schema::hasColumn('payments', 'payable_months')) {
            Schema::table('payments', function (Blueprint $table) {
                $table->text('payable_months')->nullable()->after('due_interest');
            });
        }

        if (!Schema::hasColumn('payment_details', 'payable_months')) {
            Schema::table('payment_details', function (Blueprint $table) {
                $table->text('payable_months')->nullable()->after('paid_at');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasColumn('payments', 'payable_months')) {
            Schema::table('payments', function (Blueprint $table) {
                $table->dropColumn('payable_months');
            });
        }

        if (Schema::hasColumn('payment_details', 'payable_months')) {
            Schema::table('payment_details', function (Blueprint $table) {
                $table->dropColumn('payable_months');
            });
        }
    }
};
