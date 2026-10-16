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
            // 1. Branding & Identity
            $table->string('tagline')->nullable()->after('slug');
            $table->string('logo')->nullable()->after('tagline');
            $table->string('cover_image')->nullable()->after('logo');

            // 2. Contact & Legal Tax Info
            $table->string('secondary_phone')->nullable()->after('phone');
            $table->string('city')->nullable()->after('address');
            $table->string('state')->nullable()->after('city');
            $table->string('postal_code')->nullable()->after('state');
            $table->string('tax_id')->nullable()->after('postal_code');

            // 3. 80mm Thermal Receipt Template
            $table->text('receipt_header')->nullable()->after('tax_id');
            $table->text('receipt_footer')->nullable()->after('receipt_header');
            $table->boolean('show_logo_on_receipt')->default(true)->after('receipt_footer');
            $table->boolean('show_tax_id_on_receipt')->default(true)->after('show_logo_on_receipt');

            // 4. Operating Hours & Service Channels
            $table->time('opening_time')->default('09:00:00')->after('show_tax_id_on_receipt');
            $table->time('closing_time')->default('22:00:00')->after('opening_time');
            $table->json('operating_days')->nullable()->after('closing_time');
            $table->boolean('is_dine_in_enabled')->default(true)->after('operating_days');
            $table->boolean('is_takeaway_enabled')->default(true)->after('is_dine_in_enabled');
            $table->boolean('is_qr_order_enabled')->default(true)->after('is_takeaway_enabled');

            // 5. Financial & Hardware Configuration (Rule 6: MMK)
            $table->string('currency', 10)->default('MMK')->after('is_qr_order_enabled');
            $table->string('currency_symbol', 10)->default('Ks ')->after('currency');
            $table->decimal('default_tax_percent', 5, 2)->default(5.00)->after('currency_symbol');
            $table->decimal('default_service_charge_percent', 5, 2)->default(5.00)->after('default_tax_percent');
            $table->string('printer_paper_width', 10)->default('80mm')->after('default_service_charge_percent');
            $table->boolean('auto_kick_drawer')->default(true)->after('printer_paper_width');
            $table->boolean('auto_print_receipt')->default(true)->after('auto_kick_drawer');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('restaurants', function (Blueprint $table) {
            $table->dropColumn([
                'tagline',
                'logo',
                'cover_image',
                'secondary_phone',
                'city',
                'state',
                'postal_code',
                'tax_id',
                'receipt_header',
                'receipt_footer',
                'show_logo_on_receipt',
                'show_tax_id_on_receipt',
                'opening_time',
                'closing_time',
                'operating_days',
                'is_dine_in_enabled',
                'is_takeaway_enabled',
                'is_qr_order_enabled',
                'currency',
                'currency_symbol',
                'default_tax_percent',
                'default_service_charge_percent',
                'printer_paper_width',
                'auto_kick_drawer',
                'auto_print_receipt',
            ]);
        });
    }
};
