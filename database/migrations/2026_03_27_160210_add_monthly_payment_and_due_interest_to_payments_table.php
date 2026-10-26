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
        Schema::table('payments', function (Blueprint $table) {
            if (!Schema::hasColumn('payments', 'monthly_payment')) {
                $table->boolean('monthly_payment')->default(0)->after('year');
            }
            if (!Schema::hasColumn('payments', 'due_interest')) {
                $table->boolean('due_interest')->default(0)->after('monthly_payment');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            if (Schema::hasColumn('payments', 'monthly_payment')) {
                $table->dropColumn('monthly_payment');
            }
            if (Schema::hasColumn('payments', 'due_interest')) {
                $table->dropColumn('due_interest');
            }
        });
    }
};
