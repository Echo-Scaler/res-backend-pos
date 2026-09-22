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
            'table_occupancy' => $this->getTableOccupancy($restaurant),
            'recent_orders' => $this->getRecentOrders($restaurant),
            'featured_dish' => $this->getFeaturedDish($restaurant),
            'floor_tables' => $this->getFloorTables($restaurant),
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

    /**
     * 11. Table occupancy overview.
     *
     * @return array<string, mixed>
     */
    public function getTableOccupancy(Restaurant $restaurant): array
    {
        return [
            'total_tables' => 24,
            'occupied' => 18,
            'available' => 4,
            'reserved' => 2,
            'rate_percentage' => 75,
        ];
    }

    /**
     * 12. Recent dining orders for PreAdmin styled table.
     *
     * @return array<int, array<string, mixed>>
     */
    public function getRecentOrders(Restaurant $restaurant): array
    {
        return [
            [
                'order_code' => '#ORD-2045',
                'table' => 'Table 04 (Main Hall)',
                'customer_name' => 'Ko Aung Min',
                'customer_avatar' => 'https://ui-avatars.com/api/?name=Aung+Min&background=3b82f6&color=fff&bold=true',
                'items_count' => 4,
                'dining_type' => 'Dine-in',
                'amount' => 48500,
                'status' => 'IN_DINING',
                'status_label' => 'In Dining',
                'payment_method' => 'KBZPay QR',
                'time' => '12:45 PM',
            ],
            [
                'order_code' => '#ORD-2044',
                'table' => 'Table 12 (VIP Room)',
                'customer_name' => 'Daw Thuzar',
                'customer_avatar' => 'https://ui-avatars.com/api/?name=Thuzar&background=ec4899&color=fff&bold=true',
                'items_count' => 7,
                'dining_type' => 'Dine-in',
                'amount' => 112000,
                'status' => 'BILLING',
                'status_label' => 'Billing',
                'payment_method' => 'WavePay',
                'time' => '12:30 PM',
            ],
            [
                'order_code' => '#ORD-2043',
                'table' => 'Counter / Bar',
                'customer_name' => 'U Hla Win',
                'customer_avatar' => 'https://ui-avatars.com/api/?name=Hla+Win&background=10b981&color=fff&bold=true',
                'items_count' => 2,
                'dining_type' => 'Takeaway',
                'amount' => 16500,
                'status' => 'COMPLETED',
                'status_label' => 'Completed',
                'payment_method' => 'Cash',
                'time' => '12:15 PM',
            ],
            [
                'order_code' => '#ORD-2042',
                'table' => 'Table 07 (Terrace)',
                'customer_name' => 'Ma Sandar',
                'customer_avatar' => 'https://ui-avatars.com/api/?name=Sandar&background=f97316&color=fff&bold=true',
                'items_count' => 5,
                'dining_type' => 'Dine-in',
                'amount' => 64000,
                'status' => 'COMPLETED',
                'status_label' => 'Completed',
                'payment_method' => 'Visa Card',
                'time' => '11:50 AM',
            ],
            [
                'order_code' => '#ORD-2041',
                'table' => 'Table 02 (Main Hall)',
                'customer_name' => 'Ko Sai Yan',
                'customer_avatar' => 'https://ui-avatars.com/api/?name=Sai+Yan&background=8b5cf6&color=fff&bold=true',
                'items_count' => 3,
                'dining_type' => 'Dine-in',
                'amount' => 32000,
                'status' => 'COMPLETED',
                'status_label' => 'Completed',
                'payment_method' => 'KBZPay QR',
                'time' => '11:25 AM',
            ],
        ];
    }

    /**
     * 13. Featured or Chef's recommendation dish.
     *
     * @return array<string, mixed>
     */
    public function getFeaturedDish(Restaurant $restaurant): array
    {
        return [
            'name' => 'Shan Noodle Special Set',
            'category' => 'Main Chef Special',
            'price' => 6000,
            'formatted_price' => '6,000 MMK',
            'prep_time' => '10 Mins',
            'spice_level' => 'Mild Spicy',
            'servings' => '1-2 Person',
            'sold_qty' => 42,
            'rating' => 4.9,
            'image' => 'https://images.unsplash.com/photo-1569718212165-3a8278d5f624?auto=format&fit=crop&w=600&q=80',
        ];
    }

    /**
     * 14. Floor table states for the restaurant floor visualizer.
     *
     * @return array<int, array<string, mixed>>
     */
    public function getFloorTables(Restaurant $restaurant): array
    {
        return [
            ['id' => 'T-01', 'name' => 'Table 01', 'capacity' => '4 Pax', 'status' => 'OCCUPIED', 'server' => 'Su Su', 'orders_count' => 3, 'spent' => '34,000 MMK', 'elapsed' => '35 min'],
            ['id' => 'T-02', 'name' => 'Table 02', 'capacity' => '2 Pax', 'status' => 'AVAILABLE', 'server' => '-', 'orders_count' => 0, 'spent' => '0 MMK', 'elapsed' => '-'],
            ['id' => 'T-03', 'name' => 'Table 03', 'capacity' => '4 Pax', 'status' => 'BILLING', 'server' => 'Min Min', 'orders_count' => 4, 'spent' => '52,500 MMK', 'elapsed' => '55 min'],
            ['id' => 'T-04', 'name' => 'Table 04', 'capacity' => '6 Pax', 'status' => 'OCCUPIED', 'server' => 'Su Su', 'orders_count' => 5, 'spent' => '78,000 MMK', 'elapsed' => '20 min'],
            ['id' => 'T-05', 'name' => 'Table 05', 'capacity' => '4 Pax', 'status' => 'RESERVED', 'server' => 'Kyaw Kyaw', 'orders_count' => 0, 'spent' => 'Deposit Paid', 'elapsed' => '1:30 PM'],
            ['id' => 'T-06', 'name' => 'Table 06', 'capacity' => '2 Pax', 'status' => 'AVAILABLE', 'server' => '-', 'orders_count' => 0, 'spent' => '0 MMK', 'elapsed' => '-'],
            ['id' => 'T-07', 'name' => 'Table 07', 'capacity' => '8 Pax', 'status' => 'OCCUPIED', 'server' => 'Aung Aung', 'orders_count' => 8, 'spent' => '142,000 MMK', 'elapsed' => '45 min'],
            ['id' => 'T-08', 'name' => 'Table 08', 'capacity' => '4 Pax', 'status' => 'AVAILABLE', 'server' => '-', 'orders_count' => 0, 'spent' => '0 MMK', 'elapsed' => '-'],
        ];
    }
}
