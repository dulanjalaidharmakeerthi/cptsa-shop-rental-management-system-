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
        Schema::table('audit_logs', function (Blueprint $table) {
            if (!Schema::hasColumn('audit_logs', 'actor_role')) {
                $table->string('actor_role')->nullable()->after('actor_name');
            }
            if (!Schema::hasColumn('audit_logs', 'actor_contact')) {
                $table->string('actor_contact')->nullable()->after('actor_role');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('audit_logs', function (Blueprint $table) {
            if (Schema::hasColumn('audit_logs', 'actor_contact')) {
                $table->dropColumn('actor_contact');
            }
            if (Schema::hasColumn('audit_logs', 'actor_role')) {
                $table->dropColumn('actor_role');
            }
        });
    }
};