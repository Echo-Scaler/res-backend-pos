<?php

namespace App\Services;

use App\Models\Restaurant;
use Carbon\Carbon;

class OwnerDashboardMetricsService
{
    /**
     * Get all executive dashboard metrics for the given restaurant.
     *
     * @return array<string, mixed>
     */
    public function getMetrics(Restaurant $restaurant): array
    {
        return [
            'today_sales' => $this->getTodaySales($restaurant),
            'today_orders' => $this->getTodayOrdersCount($restaurant),
            'average_order_value' => $this->getAverageOrderValue($restaurant),
            'payment_breakdown' => $this->getPaymentBreakdown($restaurant),
            'best_selling_products' => $this->getBestSellingProducts($restaurant),
            'low_stock_items' => $this->getLowStockItems($restaurant),
            'cancelled_refunded_orders' => $this->getCancelledAndRefundedOrders($restaurant),
            'staff_activity' => $this->getStaffActivity($restaurant),
            'sales_by_date' => $this->getSalesByDate($restaurant),
            'sales_by_category' => $this->getSalesByCategory($restaurant),
        ];
    }

    /**
     * 1. Today's sales total in MMK.
     */
    public function getTodaySales(Restaurant $restaurant): int
    {
        // When Order model is migrated, sum today's completed orders:
        // Order::where('restaurant_id', $restaurant->id)->whereDate('created_at', today())->where('status', 'COMPLETED')->sum('total');
        return 1450000; // MMK
    }

    /**
     * 2. Today's orders count.
     */
    public function getTodayOrdersCount(Restaurant $restaurant): int
    {
        return 86;
    }

    /**
     * 3. Average order value (AOV).
     */
    public function getAverageOrderValue(Restaurant $restaurant): int
    {
        $sales = $this->getTodaySales($restaurant);
        $orders = $this->getTodayOrdersCount($restaurant);

        return $orders > 0 ? (int) round($sales / $orders) : 0; // ~16,860 MMK
    }

    /**
     * 4. Payment breakdown by method.
     *
     * @return array<int, array<string, mixed>>
     */
    public function getPaymentBreakdown(Restaurant $restaurant): array
    {
        return [
            [
                'method' => 'KBZPay QR',
                'icon' => '📱',
                'amount' => 625000,
                'percentage' => 43,
                'color' => '#3b82f6',
            ],
            [
                'method' => 'Cash',
                'icon' => '💵',
                'amount' => 435000,
                'percentage' => 30,
                'color' => '#10b981',
            ],
            [
                'method' => 'WavePay',
                'icon' => '💛',
                'amount' => 246500,
                'percentage' => 17,
                'color' => '#eab308',
            ],
            [
                'method' => 'Visa / MPU Card',
                'icon' => '💳',
                'amount' => 143500,
                'percentage' => 10,
                'color' => '#8b5cf6',
            ],
        ];
    }

    /**
     * 5. Best-selling dishes and items.
     *
     * @return array<int, array<string, mixed>>
     */
    public function getBestSellingProducts(Restaurant $restaurant): array
    {
        return [
            [
                'name' => 'Shan Noodle Special (ဝက်/ကြက်)',
                'category' => 'Main Dishes',
                'sold_qty' => 42,
                'revenue' => 252000,
            ],
            [
                'name' => 'Kyay Oh Sikyet (ကြေးအိုးဆီချက်)',
                'category' => 'Main Dishes',
                'sold_qty' => 38,
                'revenue' => 304000,
            ],
            [
                'name' => 'Burmese Milk Tea (လက်ဖက်ရည် ချိုကျ)',
                'category' => 'Beverages',
                'sold_qty' => 64,
                'revenue' => 128000,
            ],
            [
                'name' => 'Fried Crispy Chicken (ကြက်ကြော်)',
                'category' => 'Appetizers',
                'sold_qty' => 29,
                'revenue' => 203000,
            ],
            [
                'name' => 'Falooda Royal (ဖာလူဒါ)',
                'category' => 'Desserts',
                'sold_qty' => 21,
                'revenue' => 94500,
            ],
        ];
    }

    /**
     * 6. Low-stock inventory items requiring immediate reorder.
     *
     * @return array<int, array<string, mixed>>
     */
    public function getLowStockItems(Restaurant $restaurant): array
    {
        return [
            [
                'item' => 'Cooking Oil (ဆီ)',
                'current_stock' => '4 Liters',
                'threshold' => '15 Liters',
                'status' => 'CRITICAL',
            ],
            [
                'item' => 'Fresh Chicken Breast (ကြက်ရင်အုံသား)',
                'current_stock' => '3.5 Kg',
                'threshold' => '10 Kg',
                'status' => 'CRITICAL',
            ],
            [
                'item' => 'Condensed Milk (နို့ဆီ)',
                'current_stock' => '8 Cans',
                'threshold' => '24 Cans',
                'status' => 'WARNING',
            ],
            [
                'item' => 'Takeaway Boxes (ပါဆယ်ဘူး)',
                'current_stock' => '45 Units',
                'threshold' => '150 Units',
                'status' => 'WARNING',
            ],
        ];
    }

    /**
     * 7. Cancelled and refunded orders.
     *
     * @return array<int, array<string, mixed>>
     */
    public function getCancelledAndRefundedOrders(Restaurant $restaurant): array
    {
        return [
            [
                'order_code' => '#ORD-1082',
                'table' => 'T-04',
                'amount' => 24500,
                'reason' => 'Guest emergency departure before kitchen prep',
                'status' => 'CANCELLED',
                'time' => '11:42 AM',
            ],
            [
                'order_code' => '#ORD-1065',
                'table' => 'T-11',
                'amount' => 12000,
                'reason' => 'Duplicate item entered mistakenly by floor staff',
                'status' => 'REFUNDED',
                'time' => '10:15 AM',
            ],
        ];
    }

    /**
     * 8. Real-time staff activity.
     *
     * @return array<int, array<string, mixed>>
     */
    public function getStaffActivity(Restaurant $restaurant): array
    {
        $users = $restaurant->users()->with('roles')->take(5)->get();

        $activity = [];
        foreach ($users as $user) {
            $roleName = $user->getRoleNames()->first() ?? 'STAFF';
            $activity[] = [
                'name' => $user->name,
                'role' => $roleName,
                'status' => 'Online',
                'action' => match ($roleName) {
                    'OWNER' => 'Reviewing daily revenue analytics & audit logs',
                    'MANAGER' => 'Floor supervision & shift management',
                    'CASHIER' => 'Counter checkout & cash drawer session #12',
                    'STAFF' => 'Floor order taking for Zone A tables',
                    default => 'Active on POS system',
                },
                'last_active' => 'Just now',
            ];
        }

        return $activity;
    }

    /**
     * 9. Sales by date (Last 7 days).
     *
     * @return array<int, array<string, mixed>>
     */
    public function getSalesByDate(Restaurant $restaurant): array
    {
        $sales = [];
        $base = [980000, 1150000, 1280000, 1050000, 1620000, 1890000, 1450000];

        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::today()->subDays($i);
            $amount = $base[6 - $i];
            $sales[] = [
                'date' => $date->format('M d'),
                'day' => $date->format('D'),
                'is_today' => $i === 0,
                'amount' => $amount,
                'formatted' => number_format($amount).' MMK',
                'percentage' => (int) round(($amount / 2000000) * 100),
            ];
        }

        return $sales;
    }

    /**
     * 10. Sales by category.
     *
     * @return array<int, array<string, mixed>>
     */
    public function getSalesByCategory(Restaurant $restaurant): array
    {
        return [
            [
                'name' => 'Main Dishes & Noodles',
                'icon' => '🍜',
                'percentage' => 52,
                'amount' => 754000,
                'color' => '#f97316',
            ],
            [
                'name' => 'Beverages & Coffee',
                'icon' => '☕',
                'percentage' => 24,
                'amount' => 348000,
                'color' => '#06b6d4',
            ],
            [
                'name' => 'Appetizers & Sides',
                'icon' => '🍟',
                'percentage' => 15,
                'amount' => 217500,
                'color' => '#10b981',
            ],
            [
                'name' => 'Desserts & Sweets',
                'icon' => '🍨',
                'percentage' => 9,
                'amount' => 130500,
                'color' => '#ec4899',
            ],
        ];
    }
}
