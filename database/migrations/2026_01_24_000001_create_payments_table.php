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
        if (!Schema::hasTable('payments')) {
            Schema::create('payments', function (Blueprint $table) {
                $table->id();
                // shops.id is an unsigned integer (increments), so use unsignedInteger for FK
                $table->unsignedInteger('shop_id');
                $table->decimal('amount', 10, 2)->default(0);
                $table->decimal('interest', 10, 2)->default(0);
                $table->timestamp('paid_at')->nullable();
                $table->timestamps();

                $table->foreign('shop_id')->references('id')->on('shops')->onDelete('cascade');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
