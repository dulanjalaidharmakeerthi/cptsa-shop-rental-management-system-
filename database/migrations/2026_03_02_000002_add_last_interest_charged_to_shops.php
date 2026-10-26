<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Add last_interest_charged date so we only apply the monthly penalty once.
     */
    public function up()
    {
        Schema::table('shops', function (Blueprint $table) {
            $table->date('last_interest_charged')->nullable()->after('last_rent_charged');
        });
    }

    public function down()
    {
        Schema::table('shops', function (Blueprint $table) {
            $table->dropColumn('last_interest_charged');
        });
    }
};
