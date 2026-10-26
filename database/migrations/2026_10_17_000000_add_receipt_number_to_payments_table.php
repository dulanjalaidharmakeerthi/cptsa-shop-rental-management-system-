<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('payments', 'receipt_number')) {
            Schema::table('payments', function (Blueprint $table) {
                $table->string('receipt_number')->nullable();
            });
        }

        if (Schema::hasTable('payment_details')) {
            DB::table('payment_details')
                ->whereNotNull('payment_id')
                ->whereNotNull('receipt_number')
                ->orderBy('id')
                ->chunkById(500, function ($details): void {
                    foreach ($details as $detail) {
                        DB::table('payments')
                            ->where('id', $detail->payment_id)
                            ->whereNull('receipt_number')
                            ->update(['receipt_number' => $detail->receipt_number]);
                    }
                });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('payments', 'receipt_number')) {
            Schema::table('payments', function (Blueprint $table) {
                $table->dropColumn('receipt_number');
            });
        }
    }
};
