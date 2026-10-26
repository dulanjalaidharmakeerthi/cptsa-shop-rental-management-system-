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
        Schema::create('shop_owner_histories', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('shop_id')->index();
            $table->string('shop_owner_name', 150);
            $table->string('shop_owner_nic', 100)->nullable();
            $table->string('shop_owner_phone', 50)->nullable();
            $table->string('shop_owner_address')->nullable();
            $table->string('shop_address')->nullable();
            $table->date('agreement_start')->nullable();
            $table->date('agreement_end')->nullable();
            $table->string('reason')->nullable();
            $table->timestamp('changed_at')->useCurrent();
            $table->timestamps();

            $table->foreign('shop_id')->references('id')->on('shops')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('shop_owner_histories');
    }
};
