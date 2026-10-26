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
        if (Schema::hasTable('shops')) {
            Schema::table('shops', function (Blueprint $table) {
                if (!Schema::hasColumn('shops', 'saving')) {
                    $table->decimal('saving', 12, 2)->default(0.00)->after('opening_balance');
                }
                if (!Schema::hasColumn('shops', 'due_payment')) {
                    $table->decimal('due_payment', 12, 2)->default(0.00)->after('saving');
                }
                if (!Schema::hasColumn('shops', 'due_interest')) {
                    $table->decimal('due_interest', 12, 2)->default(0.00)->after('due_payment');
                }

                if (!Schema::hasColumn('shops', 'year_charge')) {
                    $table->decimal('year_charge', 12, 2)->default(0.00)->after('due_interest');
                }
                if (!Schema::hasColumn('shops', 'year_interest')) {
                    $table->decimal('year_interest', 12, 2)->default(0.00)->after('year_charge');
                }
                if (!Schema::hasColumn('shops', 'year_total_taken')) {
                    $table->decimal('year_total_taken', 12, 2)->default(0.00)->after('year_interest');
                }

                if (!Schema::hasColumn('shops', 'deduct_payment')) {
                    $table->decimal('deduct_payment', 12, 2)->default(0.00)->after('year_total_taken');
                }
                if (!Schema::hasColumn('shops', 'deduct_interest')) {
                    $table->decimal('deduct_interest', 12, 2)->default(0.00)->after('deduct_payment');
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('shops')) {
            Schema::table('shops', function (Blueprint $table) {
                foreach (['saving','due_payment','due_interest','year_charge','year_interest','year_total_taken','deduct_payment','deduct_interest'] as $col) {
                    if (Schema::hasColumn('shops', $col)) {
                        $table->dropColumn($col);
                    }
                }
            });
        }
    }
};
