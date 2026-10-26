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
            $table->string('image1_path', 255)->nullable()->after('image_path');
            $table->string('image2_path', 255)->nullable()->after('image1_path');
            $table->string('image3_path', 255)->nullable()->after('image2_path');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('shops', function (Blueprint $table) {
            $table->dropColumn(['image1_path', 'image2_path', 'image3_path']);
        });
    }
};