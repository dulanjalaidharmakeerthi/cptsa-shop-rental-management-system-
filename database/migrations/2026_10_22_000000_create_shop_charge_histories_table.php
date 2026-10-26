<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('shop_charge_histories', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('shop_id');
            $table->json('old_values');
            $table->json('new_values');
            $table->string('changed_by')->nullable();
            $table->timestamps();

            $table->foreign('shop_id')->references('id')->on('shops')->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('shop_charge_histories');
    }
};
