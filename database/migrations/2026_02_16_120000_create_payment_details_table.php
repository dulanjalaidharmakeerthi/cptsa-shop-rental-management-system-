<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('payment_details', function (Blueprint $table) {
            $table->id();
            // use plain unsignedBigInteger columns to avoid FK issues across existing schema
            $table->unsignedBigInteger('payment_id')->nullable();
            $table->unsignedBigInteger('shop_id')->nullable();
            $table->string('shop_number')->nullable();
            $table->string('owner_name')->nullable();
            $table->decimal('amount', 10, 2)->default(0);
            $table->decimal('interest', 10, 2)->default(0);
            $table->string('receipt_number')->nullable()->index();
            $table->timestamp('paid_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('payment_details');
    }
};
