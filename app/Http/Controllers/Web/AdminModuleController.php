<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AdminModuleController extends Controller
{
    /**
     * Display the specified module dashboard / management workspace.
     */
    public function show(Request $request, ?string $module = null): View
    {
        $module = $module ?: (string) $request->route('module');
        $user = $request->user();
        $restaurant = $user->restaurant;

        $modulesConfig = $this->getModulesConfiguration();

        if (! array_key_exists($module, $modulesConfig)) {
            abort(404, 'Management module not found.');
        }

        $currentModule = $modulesConfig[$module];

        return view('admin.modules.placeholder', [
            'moduleKey' => $module,
            'module' => $currentModule,
            'restaurant' => $restaurant,
            'user' => $user,
        ]);
    }

    /**
     * Map of Owner back-office modules and metadata.
     *
     * @return array<string, array<string, mixed>>
     */
    public function getModulesConfiguration(): array
    {
        return [
            'restaurant-settings' => [
                'title' => 'Restaurant Settings',
                'icon' => '⚙️',
                'category' => 'Administration & System',
                'description' => '',
                'features' => [
                    'Store Profile & Brand Logo',
                    'Contact Phone & Support Email',
                    'Branch Operating Hours & Days',
                    'Currency & Locale Configuration (MMK)',
                ],
            ],
            'roles-permissions' => [
                'title' => 'Roles & Permissions',
                'icon' => '🛡️',
                'category' => 'Staff & Security',
                'description' => 'Granular Spatie role-based access control matrix (RBAC). Configure permissions for Owner, Manager, Cashier, and Floor Waiters.',
                'features' => [
                    'Owner Unrestricted Executive Access',
                    'Manager Floor & Staff Supervision Authority',
                    'Cashier POS Checkout & Cash Drawer Permissions',
                    'Dining Staff Tableside Ordering Permissions',
                ],
            ],
            'menu' => [
                'title' => 'Menu / Product Management',
                'icon' => '🍕',
                'category' => 'Core Operations',
                'description' => 'Categorize food menus, combo set meals, kitchen station routing (Bar, Hot Kitchen, Salad), and pricing modifiers.',
                'features' => [
                    'Food & Beverage Category Hierarchy',
                    'Dish Modifiers (Spiciness, Size, Add-ons)',
                    'Live Kitchen Printer Routing',
                    'Daily Availability & Out-of-Stock (86) Toggles',
                ],
            ],
            'inventory' => [
                'title' => 'Inventory Management',
                'icon' => '📦',
                'category' => 'Financials & Stock',
                'description' => 'Track raw kitchen ingredients, recipes, unit deductions per meal served, and automatic low-stock alerts.',
                'features' => [
                    'Raw Ingredient Stock Counts (Kg, Liters, Units)',
                    'Automatic Recipe Ingredient Deductions',
                    'Low-Stock Safety Threshold Warnings',
                    'Supplier Purchase Order Tracking',
                ],
            ],
            'tables' => [
                'title' => 'Table Management',
                'icon' => '🪑',
                'category' => 'Core Operations',
                'description' => '',
                'features' => [
                    'Zone & Section Mapping (Indoor, Outdoor, VIP)',
                    'Live Table Status (Vacant, Occupied, Billed, Dirty)',
                    'Dynamic QR Code Generation for Tableside Orders',
                    'Guest Capacity & Reservation Assignments',
                ],
            ],
            'orders' => [
                'title' => 'Order Management',
                'icon' => '🧾',
                'category' => 'Core Operations',
                'description' => '',
                'features' => [
                    'Real-Time Kitchen Display System (KDS)',
                    'Split Bill & Merge Table Orders',
                    'Order Void & Cancellation Approvals',
                    'Kitchen Preparation Timer Tracking',
                ],
            ],
            'payments' => [
                'title' => 'Payment Management',
                'icon' => '💳',
                'category' => 'Financials & Stock',
                'description' => '',
                'features' => [
                    'Shift Cash Drawer Opening & Closing Reconciliation',
                    'Dynamic Myanmar QR Code Pay (KBZPay & WavePay)',
                    'Card Payment Terminal Settlement',
                    'Split Payment & Gratuity Calculation',
                ],
            ],
            'customers' => [
                'title' => 'Customer Management',
                'icon' => '👤',
                'category' => 'Staff & Customers',
                'description' => '',
                'features' => [
                    'Customer Dining Profile & Contact Book',
                    'Loyalty Points Accumulation & Redemption',
                    'VIP & Regular Guest Recognition',
                    'Visit History & Favorite Dishes Record',
                ],
            ],
            'promotions' => [
                'title' => 'Discounts / Promotions',
                'icon' => '🏷️',
                'category' => 'Staff & Customers',
                'description' => 'Happy hour time-based deals, percentage discounts, promotional coupon codes, and bundle combo discounts.',
                'features' => [
                    'Happy Hour Scheduled Automatic Discounts',
                    'Percentage (%) & Fixed Amount Coupons',
                    'Manager Authorized Manual Discount Caps',
                    'Member Special Discount Campaigns',
                ],
            ],
            'reports' => [
                'title' => 'Reports & Analytics',
                'icon' => '📈',
                'category' => 'Financials & Stock',
                'description' => '',
                'features' => [
                    'Daily, Weekly & Monthly Gross / Net Revenue',
                    'Hourly Sales Heatmap & Turnaround Time',
                    'Server & Staff Performance Reports',
                    'Export to Excel & PDF Audits',
                ],
            ],
            'expenses' => [
                'title' => 'Expense Management',
                'icon' => '💰',
                'category' => 'Financials & Stock',
                'description' => '',
                'features' => [
                    'Daily Fresh Market Grocery Cash Outflow',
                    'Utility Bills (Electricity, Gas, Generator Fuel)',
                    'Expense Receipt Image Upload & Categorization',
                    'Net Profit Calculation (Revenue minus Expenses)',
                ],
            ],
            'tax-settings' => [
                'title' => 'Tax / Service Charge Settings',
                'icon' => '📑',
                'category' => 'Administration & System',
                'description' => '',
                'features' => [
                    'Government Commercial Tax Configuration',
                    'Dining Service Charge Percentage',
                    'Inclusive vs Exclusive Item Pricing Toggles',
                    'Official Tax Stamp Receipt Printing',
                ],
            ],
            'business-settings' => [
                'title' => 'Business Settings',
                'icon' => '🏢',
                'category' => 'Administration & System',
                'description' => '',
                'features' => [
                    'Receipt Thermal Printer Routing (58mm / 80mm)',
                    'Kitchen KDS Buzzer & Bell Frequency',
                    'Cash Drawer Automatic Kick Triggers',
                    'Offline POS Cache & Data Sync Settings',
                ],
            ],
            'audit-logs' => [
                'title' => 'Audit Logs',
                'icon' => '📜',
                'category' => 'Administration & System',
                'description' => '',
                'features' => [
                    'Staff Login & Terminal Unlock Timestamps',
                    'Bill Void & Order Discount Authorizations',
                    'Menu Price & Inventory Adjustment Trails',
                    'Security IP Address & Device Identifier Tracking',
                ],
            ],
            'account-security' => [
                'title' => 'Account / Security',
                'icon' => '🔐',
                'category' => 'Administration & System',
                'description' => '',
                'features' => [
                    'Owner Master Password & Recovery Keys',
                    'Two-Factor Authentication (2FA) Setup',
                    'Active Session & Device Revocation',
                    'Database Automatic Backup Scheduling',
                ],
            ],
        ];
    }
}
