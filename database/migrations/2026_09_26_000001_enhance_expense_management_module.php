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
        // 1. Expense Categories Table
        Schema::create('expense_categories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('restaurant_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->string('code')->nullable();
            $table->string('description')->nullable();
            $table->string('gl_account_code')->nullable()->comment('General Ledger code for accounting integration');
            $table->string('color', 20)->default('#9ec63b');
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index(['restaurant_id', 'is_active']);
        });

        // 2. Vendors Table (Suppliers & Service Providers)
        Schema::create('vendors', function (Blueprint $table) {
            $table->id();
            $table->foreignId('restaurant_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->string('code')->nullable();
            $table->string('contact_person')->nullable();
            $table->string('phone')->nullable();
            $table->string('email')->nullable();
            $table->text('address')->nullable();
            $table->string('tax_id')->nullable()->comment('Tax registration or commercial tax ID');
            $table->string('bank_name')->nullable();
            $table->string('bank_account_number')->nullable();
            $table->unsignedInteger('payment_terms_days')->default(30);
            $table->text('notes')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index(['restaurant_id', 'is_active']);
            $table->index(['restaurant_id', 'name']);
        });

        // 3. Recurring Expense Templates
        Schema::create('recurring_expenses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('restaurant_id')->constrained()->cascadeOnDelete();
            $table->foreignId('category_id')->nullable()->constrained('expense_categories')->nullOnDelete();
            $table->foreignId('vendor_id')->nullable()->constrained('vendors')->nullOnDelete();
            $table->string('title');
            $table->unsignedBigInteger('amount')->default(0)->comment('Amount in MMK');
            $table->string('frequency')->default('MONTHLY'); // DAILY, WEEKLY, MONTHLY, QUARTERLY, YEARLY
            $table->date('start_date');
            $table->date('end_date')->nullable();
            $table->date('next_due_date');
            $table->timestamp('last_generated_at')->nullable();
            $table->string('payment_method')->nullable();
            $table->boolean('auto_submit')->default(false);
            $table->boolean('is_active')->default(true);
            $table->text('notes')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->index(['restaurant_id', 'is_active', 'next_due_date']);
        });

        // 4. Upgrade Expenses Table
        Schema::table('expenses', function (Blueprint $table) {
            $table->string('category')->nullable()->change();
            $table->string('expense_number')->nullable()->after('restaurant_id');
            $table->foreignId('category_id')->nullable()->after('expense_number')->constrained('expense_categories')->nullOnDelete();
            $table->foreignId('vendor_id')->nullable()->after('category_id')->constrained('vendors')->nullOnDelete();
            $table->unsignedBigInteger('tax_amount')->default(0)->after('amount')->comment('Commercial tax in MMK');
            $table->unsignedBigInteger('total_amount')->default(0)->after('tax_amount')->comment('Total in MMK');
            $table->date('due_date')->nullable()->after('expense_date');
            $table->date('payment_date')->nullable()->after('due_date');
            $table->string('payment_method')->nullable()->after('payment_date'); // CASH, BANK_TRANSFER, CREDIT_CARD, DEBIT_CARD, KBZPAY, WAVEPAY, AYA_PAY, OTHER
            $table->string('payment_reference')->nullable()->after('payment_method');
            $table->string('status')->default('DRAFT')->after('payment_reference'); // DRAFT, PENDING_APPROVAL, APPROVED, PAYMENT_PENDING, PAID, REJECTED, VOID
            $table->string('payment_status')->default('UNPAID')->after('status'); // UNPAID, PAID, VOID
            $table->text('description')->nullable()->after('payment_status');
            $table->text('reason')->nullable()->after('description');
            $table->text('rejection_reason')->nullable()->after('notes');
            $table->text('void_reason')->nullable()->after('rejection_reason');
            $table->foreignId('approved_by')->nullable()->after('created_by')->constrained('users')->nullOnDelete();
            $table->timestamp('approved_at')->nullable()->after('approved_by');
            $table->foreignId('rejected_by')->nullable()->after('approved_at')->constrained('users')->nullOnDelete();
            $table->timestamp('rejected_at')->nullable()->after('rejected_by');
            $table->foreignId('paid_by')->nullable()->after('rejected_at')->constrained('users')->nullOnDelete();
            $table->timestamp('paid_at')->nullable()->after('paid_by');
            $table->foreignId('voided_by')->nullable()->after('paid_at')->constrained('users')->nullOnDelete();
            $table->timestamp('voided_at')->nullable()->after('voided_by');
            $table->string('receipt_path')->nullable()->after('voided_at');
            $table->foreignId('recurring_expense_id')->nullable()->after('receipt_path')->constrained('recurring_expenses')->nullOnDelete();

            $table->index(['restaurant_id', 'status']);
            $table->index(['restaurant_id', 'payment_status']);
            $table->index(['restaurant_id', 'due_date']);
        });

        // 5. Expense Attachments (Receipts, Invoices, Delivery Slips)
        Schema::create('expense_attachments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('expense_id')->constrained('expenses')->cascadeOnDelete();
            $table->string('file_path');
            $table->string('file_name');
            $table->unsignedBigInteger('file_size')->default(0);
            $table->string('mime_type')->nullable();
            $table->foreignId('uploaded_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });

        // 6. Expense Transactions (Cash Register & Bank Accounts Integration)
        Schema::create('expense_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('restaurant_id')->constrained()->cascadeOnDelete();
            $table->foreignId('expense_id')->constrained('expenses')->cascadeOnDelete();
            $table->string('transaction_type')->default('CASH_DRAWER'); // CASH_DRAWER, BANK_ACCOUNT, DIGITAL_WALLET
            $table->string('payment_method')->default('CASH');
            $table->unsignedBigInteger('amount')->default(0)->comment('Amount in MMK');
            $table->string('reference_no')->nullable();
            $table->string('account_or_drawer_name')->default('Main Cash Register');
            $table->string('status')->default('SUCCESS'); // SUCCESS, REVERSED
            $table->text('notes')->nullable();
            $table->foreignId('processed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->index(['restaurant_id', 'transaction_type']);
            $table->index(['restaurant_id', 'created_at']);
        });

        // 7. Expense Budgets Table (Budget vs Actual)
        Schema::create('expense_budgets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('restaurant_id')->constrained()->cascadeOnDelete();
            $table->foreignId('category_id')->constrained('expense_categories')->cascadeOnDelete();
            $table->unsignedSmallInteger('fiscal_year');
            $table->string('period_type')->default('MONTHLY'); // MONTHLY, QUARTERLY, YEARLY
            $table->unsignedTinyInteger('period_month')->nullable()->comment('1 to 12 if MONTHLY');
            $table->unsignedBigInteger('budget_amount')->default(0)->comment('Budget limit in MMK');
            $table->unsignedTinyInteger('alert_threshold_percent')->default(80)->comment('Alert at % of budget consumed');
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->unique(
                ['restaurant_id', 'category_id', 'fiscal_year', 'period_type', 'period_month'],
                'exp_budget_unique'
            );
            $table->index(['restaurant_id', 'fiscal_year']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('expense_budgets');
        Schema::dropIfExists('expense_transactions');
        Schema::dropIfExists('expense_attachments');

        Schema::table('expenses', function (Blueprint $table) {
            $table->dropForeign(['recurring_expense_id']);
            $table->dropForeign(['voided_by']);
            $table->dropForeign(['paid_by']);
            $table->dropForeign(['rejected_by']);
            $table->dropForeign(['approved_by']);
            $table->dropForeign(['vendor_id']);
            $table->dropForeign(['category_id']);

            $table->dropIndex(['restaurant_id', 'due_date']);
            $table->dropIndex(['restaurant_id', 'payment_status']);
            $table->dropIndex(['restaurant_id', 'status']);

            $table->dropColumn([
                'expense_number',
                'category_id',
                'vendor_id',
                'tax_amount',
                'total_amount',
                'due_date',
                'payment_date',
                'payment_method',
                'payment_reference',
                'status',
                'payment_status',
                'description',
                'reason',
                'rejection_reason',
                'void_reason',
                'approved_by',
                'approved_at',
                'rejected_by',
                'rejected_at',
                'paid_by',
                'paid_at',
                'voided_by',
                'voided_at',
                'receipt_path',
                'recurring_expense_id',
            ]);
        });

        Schema::dropIfExists('recurring_expenses');
        Schema::dropIfExists('vendors');
        Schema::dropIfExists('expense_categories');
    }
};
