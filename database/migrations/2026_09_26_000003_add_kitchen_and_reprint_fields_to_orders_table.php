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
        Schema::table('orders', function (Blueprint $table) {
            $table->string('kitchen_status')->default('PENDING_COOK')->after('status'); // PENDING_COOK, COOKING, READY_FOR_DELIVERY, SERVED_TO_TABLE
            $table->unsignedInteger('reprint_count')->default(0)->after('kitchen_status');
            $table->timestamp('verified_at')->nullable()->after('reprint_count');
            $table->foreignId('verified_by')->nullable()->after('verified_at')->constrained('users')->nullOnDelete();
        });

        Schema::table('order_items', function (Blueprint $table) {
            $table->string('special_notes')->nullable()->after('item_name');
            $table->boolean('is_cooked')->default(false)->after('is_voided');
            $table->boolean('is_verified')->default(false)->after('is_cooked');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropConstrainedForeignId('verified_by');
            $table->dropColumn(['kitchen_status', 'reprint_count', 'verified_at']);
        });

        Schema::table('order_items', function (Blueprint $table) {
            $table->dropColumn(['special_notes', 'is_cooked', 'is_verified']);
        });
    }
};
