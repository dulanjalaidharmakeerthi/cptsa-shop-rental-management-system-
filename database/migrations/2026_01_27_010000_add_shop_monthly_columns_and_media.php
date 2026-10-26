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
            // Add monthly rent columns if they don't exist (safe for older DBs)
            if (!Schema::hasColumn('shops', 'rent_january')) {
                $table->decimal('rent_january', 10, 2)->default(0.00)->after('record_year');
            }
            if (!Schema::hasColumn('shops', 'rent_february')) {
                $table->decimal('rent_february', 10, 2)->default(0.00);
            }
            if (!Schema::hasColumn('shops', 'rent_march')) {
                $table->decimal('rent_march', 10, 2)->default(0.00);
            }
            if (!Schema::hasColumn('shops', 'rent_april')) {
                $table->decimal('rent_april', 10, 2)->default(0.00);
            }
            if (!Schema::hasColumn('shops', 'rent_may')) {
                $table->decimal('rent_may', 10, 2)->default(0.00);
            }
            if (!Schema::hasColumn('shops', 'rent_june')) {
                $table->decimal('rent_june', 10, 2)->default(0.00);
            }
            if (!Schema::hasColumn('shops', 'rent_july')) {
                $table->decimal('rent_july', 10, 2)->default(0.00);
            }
            if (!Schema::hasColumn('shops', 'rent_august')) {
                $table->decimal('rent_august', 10, 2)->default(0.00);
            }
            if (!Schema::hasColumn('shops', 'rent_september')) {
                $table->decimal('rent_september', 10, 2)->default(0.00);
            }
            if (!Schema::hasColumn('shops', 'rent_october')) {
                $table->decimal('rent_october', 10, 2)->default(0.00);
            }
            if (!Schema::hasColumn('shops', 'rent_november')) {
                $table->decimal('rent_november', 10, 2)->default(0.00);
            }
            if (!Schema::hasColumn('shops', 'rent_december')) {
                $table->decimal('rent_december', 10, 2)->default(0.00);
            }

            // Totals if missing
            if (!Schema::hasColumn('shops', 'total_paid')) {
                $table->decimal('total_paid', 12, 2)->default(0.00);
            }
            if (!Schema::hasColumn('shops', 'total_due')) {
                $table->decimal('total_due', 12, 2)->default(0.00);
            }
            if (!Schema::hasColumn('shops', 'balance_amount')) {
                $table->decimal('balance_amount', 12, 2)->default(0.00);
            }

            // Add location and image_path to store shop location and sample image
            if (!Schema::hasColumn('shops', 'location')) {
                $table->string('location', 255)->nullable()->after('remarks');
            }
            if (!Schema::hasColumn('shops', 'image_path')) {
                $table->string('image_path', 255)->nullable()->after('location');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('shops', function (Blueprint $table) {
            if (Schema::hasColumn('shops', 'rent_january')) {
                $table->dropColumn([
                    'rent_january','rent_february','rent_march','rent_april','rent_may','rent_june','rent_july','rent_august','rent_september','rent_october','rent_november','rent_december',
                    'total_paid','total_due','balance_amount',
                    'location','image_path'
                ]);
            }
        });
    }
};
