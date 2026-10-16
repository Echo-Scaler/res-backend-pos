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
            $table->string('category', 50)->default('GENERAL')->after('action');
            $table->string('severity', 20)->default('INFO')->after('category');
            $table->string('role_name', 50)->nullable()->after('user_name');
            $table->json('metadata')->nullable()->after('new_value');
            $table->text('user_agent')->nullable()->after('ip_address');

            $table->index(['restaurant_id', 'category']);
            $table->index(['restaurant_id', 'severity']);
            $table->index(['restaurant_id', 'action']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('audit_logs', function (Blueprint $table) {
            $table->dropIndex(['restaurant_id', 'category']);
            $table->dropIndex(['restaurant_id', 'severity']);
            $table->dropIndex(['restaurant_id', 'action']);

            $table->dropColumn([
                'category',
                'severity',
                'role_name',
                'metadata',
                'user_agent',
            ]);
        });
    }
};
