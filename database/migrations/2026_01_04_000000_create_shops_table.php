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
        if (!Schema::hasTable('shops')) {
            Schema::create('shops', function (Blueprint $table) {
                $table->increments('id');

                // Shop & Owner Details
                $table->string('shop_number', 20);
                $table->string('shop_name', 150)->default('');
                $table->string('shop_owner_name', 150);

                // Rent Details
                $table->decimal('monthly_rent', 10, 2);
                $table->decimal('advance_payment', 10, 2)->default(0.00);
                $table->decimal('opening_balance', 10, 2)->default(0.00);
                // Agreement duration (optional)
                $table->string('agreement_duration', 50)->nullable();
                // Associated bus stand (optional)
                $table->unsignedInteger('bus_stand_id')->nullable()->index();
                // Agreement start and end dates
                $table->date('agreement_start')->nullable();
                $table->date('agreement_end')->nullable();

                // Monthly Payments
                $table->decimal('rent_january', 10, 2)->default(0.00);
                $table->decimal('rent_february', 10, 2)->default(0.00);
                $table->decimal('rent_march', 10, 2)->default(0.00);
                $table->decimal('rent_april', 10, 2)->default(0.00);
                $table->decimal('rent_may', 10, 2)->default(0.00);
                $table->decimal('rent_june', 10, 2)->default(0.00);
                $table->decimal('rent_july', 10, 2)->default(0.00);
                $table->decimal('rent_august', 10, 2)->default(0.00);
                $table->decimal('rent_september', 10, 2)->default(0.00);
                $table->decimal('rent_october', 10, 2)->default(0.00);
                $table->decimal('rent_november', 10, 2)->default(0.00);
                $table->decimal('rent_december', 10, 2)->default(0.00);

                // Calculations
                $table->decimal('total_paid', 12, 2)->default(0.00);
                $table->decimal('total_due', 12, 2)->default(0.00);
                $table->decimal('balance_amount', 12, 2)->default(0.00);

                // Other
                $table->text('remarks')->nullable();
                $table->year('record_year');

                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('shops');
    }
};
