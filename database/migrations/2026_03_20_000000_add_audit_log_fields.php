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
            if (!Schema::hasColumn('audit_logs', 'actor_id')) {
                $table->unsignedBigInteger('actor_id')->nullable()->after('id');
            }
            if (!Schema::hasColumn('audit_logs', 'actor_type')) {
                $table->string('actor_type')->default('system')->after('actor_id');
            }
            if (!Schema::hasColumn('audit_logs', 'actor_name')) {
                $table->string('actor_name')->nullable()->after('actor_type');
            }
            if (!Schema::hasColumn('audit_logs', 'action')) {
                $table->string('action')->after('actor_name');
            }
            if (!Schema::hasColumn('audit_logs', 'auditable_type')) {
                $table->string('auditable_type')->nullable()->after('action');
            }
            if (!Schema::hasColumn('audit_logs', 'auditable_id')) {
                $table->unsignedBigInteger('auditable_id')->nullable()->after('auditable_type');
            }
            if (!Schema::hasColumn('audit_logs', 'auditable_name')) {
                $table->string('auditable_name')->nullable()->after('auditable_id');
            }
            if (!Schema::hasColumn('audit_logs', 'description')) {
                $table->text('description')->nullable()->after('auditable_name');
            }
            if (!Schema::hasColumn('audit_logs', 'old_values')) {
                $table->longText('old_values')->nullable()->after('description');
            }
            if (!Schema::hasColumn('audit_logs', 'new_values')) {
                $table->longText('new_values')->nullable()->after('old_values');
            }
            if (!Schema::hasColumn('audit_logs', 'ip_address')) {
                $table->string('ip_address')->nullable()->after('new_values');
            }
            if (!Schema::hasColumn('audit_logs', 'user_agent')) {
                $table->text('user_agent')->nullable()->after('ip_address');
            }
            if (!Schema::hasColumn('audit_logs', 'metadata')) {
                $table->json('metadata')->nullable()->after('user_agent');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('audit_logs', function (Blueprint $table) {
            $table->dropColumn([
                'actor_id', 'actor_type', 'actor_name', 'action', 'auditable_type', 'auditable_id', 'auditable_name', 'description', 'old_values', 'new_values', 'ip_address', 'user_agent', 'metadata'
            ]);
        });
    }
};