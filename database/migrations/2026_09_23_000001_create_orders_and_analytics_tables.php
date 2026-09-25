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
        // 1. Orders Table
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('restaurant_id')->constrained()->cascadeOnDelete();
            $table->string('order_number')->index();
            $table->foreignId('customer_id')->nullable();
            $table->string('customer_name')->nullable();
            $table->string('table_number')->nullable();
            $table->unsignedInteger('guest_count')->default(1);
            $table->string('order_type')->default('DINE_IN'); // DINE_IN, TAKEAWAY, DELIVERY, PICKUP
            $table->unsignedBigInteger('subtotal')->default(0); // Gross sales
            $table->unsignedBigInteger('discount_amount')->default(0);
            $table->unsignedBigInteger('tax_amount')->default(0);
            $table->unsignedBigInteger('cogs_amount')->default(0); // Total Cost of Goods Sold
            $table->unsignedBigInteger('total_amount')->default(0); // Net sales
            $table->unsignedBigInteger('paid_amount')->default(0);
            $table->unsignedBigInteger('refund_amount')->default(0);
            $table->unsignedBigInteger('outstanding_amount')->default(0);
            $table->string('status')->default('COMPLETED'); // COMPLETED, PENDING, CANCELLED, REFUNDED
            $table->string('payment_status')->default('PAID'); // PAID, PARTIAL, UNPAID, REFUNDED
            $table->string('cancellation_reason')->nullable();
            $table->timestamp('cancelled_at')->nullable();
            $table->foreignId('staff_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->index(['restaurant_id', 'created_at']);
            $table->index(['restaurant_id', 'status']);
        });

        // 2. Order Items Table
        Schema::create('order_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained()->cascadeOnDelete();
            $table->foreignId('product_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('category_id')->nullable()->constrained()->nullOnDelete();
            $table->string('item_name');
            $table->boolean('is_alcohol')->default(false);
            $table->unsignedInteger('quantity')->default(1);
            $table->unsignedBigInteger('unit_price')->default(0);
            $table->unsignedBigInteger('cost_price')->default(0); // COGS per unit
            $table->unsignedBigInteger('subtotal')->default(0);
            $table->unsignedBigInteger('profit')->default(0);
            $table->boolean('is_voided')->default(false);
            $table->string('void_reason')->nullable();
            $table->foreignId('voided_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->index(['order_id', 'is_alcohol']);
        });

        // 3. Payments Table
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('restaurant_id')->constrained()->cascadeOnDelete();
            $table->foreignId('order_id')->constrained()->cascadeOnDelete();
            $table->string('payment_method')->default('CASH'); // CASH, KBZPAY, WAVEPAY, CARD
            $table->unsignedBigInteger('amount')->default(0);
            $table->string('status')->default('SUCCESS'); // SUCCESS, REFUNDED
            $table->string('reference_no')->nullable();
            $table->timestamps();

            $table->index(['restaurant_id', 'payment_method']);
        });

        // 4. Expenses Table
        Schema::create('expenses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('restaurant_id')->constrained()->cascadeOnDelete();
            $table->string('category'); // FOOD_PURCHASE, LABOR, RENT, UTILITY, CLEANING, MAINTENANCE, OTHER
            $table->string('title');
            $table->unsignedBigInteger('amount')->default(0);
            $table->date('expense_date');
            $table->text('notes')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->index(['restaurant_id', 'expense_date']);
        });

        // 5. Audit Logs Table
        Schema::create('audit_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('restaurant_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('user_name')->nullable();
            $table->string('action'); // PRICE_CHANGE, VOID_ORDER, DISCOUNT_APPLIED, LOGIN
            $table->string('resource_type')->nullable();
            $table->unsignedBigInteger('resource_id')->nullable();
            $table->text('old_value')->nullable();
            $table->text('new_value')->nullable();
            $table->string('ip_address')->nullable();
            $table->timestamps();

            $table->index(['restaurant_id', 'created_at']);
        });

        // 6. Product Recipes Table (for Ingredient & COGS Costing)
        Schema::create('product_recipes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            $table->string('ingredient_name');
            $table->string('unit')->default('g');
            $table->decimal('quantity_used', 10, 2)->default(1);
            $table->unsignedBigInteger('cost_amount')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_recipes');
        Schema::dropIfExists('audit_logs');
        Schema::dropIfExists('expenses');
        Schema::dropIfExists('payments');
        Schema::dropIfExists('order_items');
        Schema::dropIfExists('orders');
    }
};
