<?php

namespace Database\Seeders;

use App\Models\ExpenseBudget;
use App\Models\ExpenseCategory;
use App\Models\RecurringExpense;
use App\Models\Restaurant;
use App\Models\User;
use App\Models\Vendor;
use Illuminate\Database\Seeder;

class ExpenseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(?Restaurant $targetRestaurant = null): void
    {
        $restaurants = $targetRestaurant ? collect([$targetRestaurant]) : Restaurant::all();

        foreach ($restaurants as $restaurant) {
            $owner = $restaurant->owner ?? User::where('restaurant_id', $restaurant->id)->first();

            // 1. Categories
            $categoriesData = [
                ['name' => 'Food Supplies (COGS)', 'code' => 'COGS-FOOD', 'gl_account_code' => '5001', 'color' => '#9ec63b', 'description' => 'Meat, vegetables, dairy, dry goods for kitchen menu preparation'],
                ['name' => 'Beverage & Alcohol', 'code' => 'COGS-BEV', 'gl_account_code' => '5002', 'color' => '#06b6d4', 'description' => 'Wine, beers, spirits, bar syrups, and cocktail ingredients'],
                ['name' => 'Kitchen Consumables', 'code' => 'OPEX-KITCHEN', 'gl_account_code' => '6001', 'color' => '#eab308', 'description' => 'Cooking oils, parchment paper, foil, disposable takeaway boxes'],
                ['name' => 'Staff Wages & Payroll', 'code' => 'OPEX-LABOR', 'gl_account_code' => '6002', 'color' => '#3b82f6', 'description' => 'Monthly staff salaries, kitchen chef wages, shift overtime payouts'],
                ['name' => 'Rent & Facilities', 'code' => 'OPEX-RENT', 'gl_account_code' => '6003', 'color' => '#ec4899', 'description' => 'Dining hall lease, outdoor terrace rental fee, property rates'],
                ['name' => 'Utilities (Gas, Electric, Water)', 'code' => 'OPEX-UTIL', 'gl_account_code' => '6004', 'color' => '#f97316', 'description' => 'Electricity, commercial cooking gas cylinders, municipal water, generator diesel'],
                ['name' => 'Maintenance & Equipment', 'code' => 'OPEX-MAINT', 'gl_account_code' => '6005', 'color' => '#8b5cf6', 'description' => 'Espresso machine servicing, kitchen exhaust maintenance, refrigerator repair'],
                ['name' => 'Marketing & Promotions', 'code' => 'OPEX-MKTG', 'gl_account_code' => '6006', 'color' => '#10b981', 'description' => 'Social media campaigns, printed menu boards, promotional flyers'],
                ['name' => 'Cleaning & Sanitation', 'code' => 'OPEX-CLEAN', 'gl_account_code' => '6007', 'color' => '#14b8a6', 'description' => 'Commercial dishwasher detergent, mop heads, trash bags, pest control'],
                ['name' => 'Software & POS Licensing', 'code' => 'OPEX-ADMIN', 'gl_account_code' => '6008', 'color' => '#64748b', 'description' => 'POS cloud server, accounting tools, broadband internet connection'],
            ];

            $categoryMap = [];
            foreach ($categoriesData as $cat) {
                $category = ExpenseCategory::firstOrCreate(
                    ['restaurant_id' => $restaurant->id, 'name' => $cat['name']],
                    array_merge($cat, ['restaurant_id' => $restaurant->id, 'is_active' => true])
                );
                $categoryMap[$cat['code']] = $category;
            }

            // 2. Vendors
            $vendorsData = [
                [
                    'name' => 'Metro Cash & Carry Wholesale',
                    'code' => 'VND-METRO',
                    'contact_person' => 'U Aung San',
                    'phone' => '+95 9 1234 5678',
                    'email' => 'sales@metroyangon.com',
                    'address' => 'Corner of Bayint Naung Rd, Insein, Yangon',
                    'tax_id' => 'TAX-MM-449102',
                    'bank_name' => 'KBZ Bank Corporate',
                    'bank_account_number' => '0451010023456789',
                    'payment_terms_days' => 30,
                ],
                [
                    'name' => 'Golden Heritage Poultry & Meats',
                    'code' => 'VND-MEAT',
                    'contact_person' => 'Daw Thida',
                    'phone' => '+95 9 2345 6789',
                    'email' => 'orders@goldenheritage.com',
                    'address' => 'Thirimingalar Wholesale Market, Yangon',
                    'tax_id' => 'TAX-MM-338291',
                    'bank_name' => 'AYA Bank Corporate',
                    'bank_account_number' => '200192837465',
                    'payment_terms_days' => 15,
                ],
                [
                    'name' => 'Yangon Gas & Energy Supply',
                    'code' => 'VND-GAS',
                    'contact_person' => 'Ko Zaw Win',
                    'phone' => '+95 9 3456 7890',
                    'email' => 'service@yangongas.com',
                    'address' => 'Industrial Zone 1, South Dagon, Yangon',
                    'tax_id' => 'TAX-MM-192834',
                    'bank_name' => 'CB Bank',
                    'bank_account_number' => '100482736451',
                    'payment_terms_days' => 7,
                ],
                [
                    'name' => 'Crystal Ice & Beverage Distributors',
                    'code' => 'VND-BEV',
                    'contact_person' => 'U Tin Tun',
                    'phone' => '+95 9 4567 8901',
                    'email' => 'dispatch@crystalbeverage.com',
                    'address' => 'Kamayut Township, Yangon',
                    'tax_id' => 'TAX-MM-837465',
                    'bank_name' => 'KBZ Bank',
                    'bank_account_number' => '0552010098765432',
                    'payment_terms_days' => 14,
                ],
                [
                    'name' => 'CleanPro Sanitation Supplies',
                    'code' => 'VND-CLEAN',
                    'contact_person' => 'Ma Nilar',
                    'phone' => '+95 9 5678 9012',
                    'email' => 'info@cleanpro.com.mm',
                    'address' => 'Hlaing Township, Yangon',
                    'tax_id' => 'TAX-MM-556677',
                    'bank_name' => 'KBZ Bank',
                    'bank_account_number' => '012398475628',
                    'payment_terms_days' => 30,
                ],
            ];

            $vendorMap = [];
            foreach ($vendorsData as $v) {
                $vendor = Vendor::firstOrCreate(
                    ['restaurant_id' => $restaurant->id, 'name' => $v['name']],
                    array_merge($v, ['restaurant_id' => $restaurant->id, 'is_active' => true])
                );
                $vendorMap[$v['code']] = $vendor;
            }

            // 3. Budgets for Current Year
            $currentYear = (int) now()->format('Y');
            $currentMonth = (int) now()->format('n');

            $monthlyBudgetLimits = [
                'COGS-FOOD' => 3500000,
                'COGS-BEV' => 1800000,
                'OPEX-KITCHEN' => 450000,
                'OPEX-LABOR' => 2800000,
                'OPEX-RENT' => 1500000,
                'OPEX-UTIL' => 600000,
                'OPEX-MAINT' => 350000,
                'OPEX-MKTG' => 300000,
                'OPEX-CLEAN' => 200000,
                'OPEX-ADMIN' => 150000,
            ];

            foreach ($monthlyBudgetLimits as $code => $limit) {
                if (isset($categoryMap[$code])) {
                    ExpenseBudget::updateOrCreate(
                        [
                            'restaurant_id' => $restaurant->id,
                            'category_id' => $categoryMap[$code]->id,
                            'fiscal_year' => $currentYear,
                            'period_type' => 'MONTHLY',
                            'period_month' => $currentMonth,
                        ],
                        [
                            'budget_amount' => $limit,
                            'alert_threshold_percent' => 85,
                            'notes' => 'Allocated operational monthly budget for '.now()->format('F Y'),
                        ]
                    );
                }
            }

            // 4. Recurring Expenses
            if (isset($categoryMap['OPEX-RENT'])) {
                RecurringExpense::firstOrCreate(
                    ['restaurant_id' => $restaurant->id, 'title' => 'Monthly Dining Facility Lease'],
                    [
                        'restaurant_id' => $restaurant->id,
                        'category_id' => $categoryMap['OPEX-RENT']->id,
                        'vendor_id' => null,
                        'title' => 'Monthly Dining Facility Lease',
                        'amount' => 1500000,
                        'frequency' => 'MONTHLY',
                        'start_date' => now()->startOfYear()->toDateString(),
                        'next_due_date' => now()->startOfMonth()->addDays(4)->toDateString(),
                        'payment_method' => 'BANK_TRANSFER',
                        'auto_submit' => true,
                        'is_active' => true,
                        'notes' => 'Fixed monthly lease agreement',
                        'created_by' => $owner?->id,
                    ]
                );
            }

            if (isset($categoryMap['OPEX-ADMIN'])) {
                RecurringExpense::firstOrCreate(
                    ['restaurant_id' => $restaurant->id, 'title' => 'Cloud POS & Internet Fiber Subscription'],
                    [
                        'restaurant_id' => $restaurant->id,
                        'category_id' => $categoryMap['OPEX-ADMIN']->id,
                        'vendor_id' => null,
                        'title' => 'Cloud POS & Internet Fiber Subscription',
                        'amount' => 120000,
                        'frequency' => 'MONTHLY',
                        'start_date' => now()->startOfYear()->toDateString(),
                        'next_due_date' => now()->startOfMonth()->addDays(9)->toDateString(),
                        'payment_method' => 'KBZPAY',
                        'auto_submit' => true,
                        'is_active' => true,
                        'notes' => 'High-speed fiber internet and POS subscription',
                        'created_by' => $owner?->id,
                    ]
                );
            }
        }
    }
}
