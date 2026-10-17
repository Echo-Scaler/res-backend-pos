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
        Schema::table('restaurants', function (Blueprint $table) {
            // 1. Tax Specific Settings
            $table->boolean('is_tax_enabled')->default(true)->after('default_tax_percent');
            $table->string('tax_name', 100)->default('Commercial Tax')->after('is_tax_enabled');
            $table->string('tax_calculation_mode', 20)->default('inclusive')->after('tax_name'); // 'inclusive' or 'exclusive'
            $table->string('tax_office_name', 150)->nullable()->after('tax_id');
            $table->boolean('apply_tax_dine_in')->default(true)->after('tax_calculation_mode');
            $table->boolean('apply_tax_takeaway')->default(true)->after('apply_tax_dine_in');
            $table->boolean('apply_tax_delivery')->default(true)->after('apply_tax_takeaway');
            $table->boolean('apply_tax_qr_order')->default(true)->after('apply_tax_delivery');

            // 2. Service Charge Specific Settings
            $table->boolean('is_service_charge_enabled')->default(true)->after('default_service_charge_percent');
            $table->string('service_charge_name', 100)->default('Service Charge')->after('is_service_charge_enabled');
            $table->boolean('is_service_charge_taxable')->default(false)->after('service_charge_name');
            $table->boolean('service_charge_dine_in_only')->default(true)->after('is_service_charge_taxable');
            $table->unsignedBigInteger('takeaway_packaging_fee')->default(0)->after('service_charge_dine_in_only');

            // 3. Rounding & Currency Rule (Rule 6: MMK)
            $table->string('rounding_method', 20)->default('round_50')->after('takeaway_packaging_fee'); // 'none', 'round_50', 'round_100'
            $table->string('rounding_direction', 20)->default('nearest')->after('rounding_method'); // 'nearest', 'ceil', 'floor'
            $table->boolean('round_cash_only')->default(true)->after('rounding_direction');

            // 4. Receipt Tax Stamp Presentation
            $table->boolean('show_tax_breakdown_on_receipt')->default(true)->after('round_cash_only');
            $table->boolean('show_service_charge_on_receipt')->default(true)->after('show_tax_breakdown_on_receipt');
            $table->boolean('print_tax_stamp_seal')->default(true)->after('show_service_charge_on_receipt');
            $table->text('tax_disclaimer_text')->nullable()->after('print_tax_stamp_seal');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('restaurants', function (Blueprint $table) {
            $table->dropColumn([
                'is_tax_enabled',
                'tax_name',
                'tax_calculation_mode',
                'tax_office_name',
                'apply_tax_dine_in',
                'apply_tax_takeaway',
                'apply_tax_delivery',
                'apply_tax_qr_order',
                'is_service_charge_enabled',
                'service_charge_name',
                'is_service_charge_taxable',
                'service_charge_dine_in_only',
                'takeaway_packaging_fee',
                'rounding_method',
                'rounding_direction',
                'round_cash_only',
                'show_tax_breakdown_on_receipt',
                'show_service_charge_on_receipt',
                'print_tax_stamp_seal',
                'tax_disclaimer_text',
            ]);
        });
    }
};
