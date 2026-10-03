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
        // 1. Cash Drawer Sessions Table (Cashier Shift Management)
        Schema::create('cash_drawer_sessions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('restaurant_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->string('terminal_code', 50)->default('POS-REG-01');
            $table->timestamp('opened_at')->useCurrent();
            $table->timestamp('closed_at')->nullable();
            $table->unsignedBigInteger('opening_float')->default(0); // MMK
            $table->unsignedBigInteger('cash_sales')->default(0); // MMK
            $table->unsignedBigInteger('digital_sales')->default(0); // MMK (KBZPay, WavePay, Card)
            $table->unsignedBigInteger('cash_in')->default(0); // MMK
            $table->unsignedBigInteger('cash_out')->default(0); // MMK
            $table->unsignedBigInteger('expected_cash')->default(0); // MMK
            $table->unsignedBigInteger('closing_actual_cash')->nullable(); // MMK
            $table->bigInteger('cash_difference')->nullable(); // MMK (Over / Short)
            $table->string('status', 20)->default('OPEN'); // OPEN, CLOSED
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index(['restaurant_id', 'status']);
            $table->index(['restaurant_id', 'user_id']);
        });

        // 2. Cash Drawer Transactions Table (Cash In / Out Float movements)
        Schema::create('cash_drawer_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cash_drawer_session_id')->constrained('cash_drawer_sessions')->cascadeOnDelete();
            $table->foreignId('restaurant_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->string('type', 20); // CASH_IN, CASH_OUT
            $table->unsignedBigInteger('amount')->default(0); // MMK
            $table->string('reason');
            $table->timestamps();

            $table->index(['cash_drawer_session_id', 'type']);
        });

        // 3. Add cash_drawer_session_id and tender details to payments and orders
        Schema::table('payments', function (Blueprint $table) {
            if (! Schema::hasColumn('payments', 'cash_drawer_session_id')) {
                $table->foreignId('cash_drawer_session_id')->nullable()->after('order_id')->constrained('cash_drawer_sessions')->nullOnDelete();
            }
            if (! Schema::hasColumn('payments', 'tendered_amount')) {
                $table->unsignedBigInteger('tendered_amount')->default(0)->after('amount');
            }
            if (! Schema::hasColumn('payments', 'change_amount')) {
                $table->unsignedBigInteger('change_amount')->default(0)->after('tendered_amount');
            }
        });

        Schema::table('orders', function (Blueprint $table) {
            if (! Schema::hasColumn('orders', 'cash_drawer_session_id')) {
                $table->foreignId('cash_drawer_session_id')->nullable()->after('staff_id')->constrained('cash_drawer_sessions')->nullOnDelete();
            }
            if (! Schema::hasColumn('orders', 'service_charge')) {
                $table->unsignedBigInteger('service_charge')->default(0)->after('tax_amount');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            if (Schema::hasColumn('orders', 'cash_drawer_session_id')) {
                $table->dropForeign(['cash_drawer_session_id']);
                $table->dropColumn('cash_drawer_session_id');
            }
            if (Schema::hasColumn('orders', 'service_charge')) {
                $table->dropColumn('service_charge');
            }
        });

        Schema::table('payments', function (Blueprint $table) {
            if (Schema::hasColumn('payments', 'cash_drawer_session_id')) {
                $table->dropForeign(['cash_drawer_session_id']);
                $table->dropColumn('cash_drawer_session_id');
            }
            if (Schema::hasColumn('payments', 'change_amount')) {
                $table->dropColumn('change_amount');
            }
            if (Schema::hasColumn('payments', 'tendered_amount')) {
                $table->dropColumn('tendered_amount');
            }
        });

        Schema::dropIfExists('cash_drawer_transactions');
        Schema::dropIfExists('cash_drawer_sessions');
    }
};
