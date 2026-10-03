<?php

namespace Database\Seeders;

use App\Models\DiningTable;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\Restaurant;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class DiningTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $restaurant = Restaurant::first();
        if (! $restaurant) {
            return;
        }

        // Clean up legacy non-numeric tables outside 1-47 for this restaurant
        DiningTable::where('restaurant_id', $restaurant->id)
            ->whereNotIn('table_number', array_map('strval', range(1, 47)))
            ->delete();

        // Constant 1 to 47 Dining Tables in the shop
        for ($i = 1; $i <= 47; $i++) {
            $tableNum = (string) $i;

            if ($i <= 20) {
                $area = 'Main Dining Hall';
                $name = $i <= 10 ? "Window Booth {$i}" : "Center Table {$i}";
                $capacity = ($i % 3 === 0) ? 6 : 4;
                $notes = $i <= 10 ? 'High natural light window booth' : 'Central dining hall table';
            } elseif ($i <= 28) {
                $area = 'Main Dining Hall';
                $name = "Couples Table {$i}";
                $capacity = 2;
                $notes = 'Intimate 2-seater dining table';
            } elseif ($i <= 36) {
                $area = 'Outdoor Terrace';
                $name = "Terrace Garden {$i}";
                $capacity = 4;
                $notes = 'Open air garden dining area';
            } elseif ($i <= 42) {
                $area = 'VIP Dining Room';
                $name = "VIP Suite {$i}";
                $capacity = ($i % 2 === 0) ? 8 : 6;
                $notes = 'Private soundproof executive room';
            } else {
                $area = 'Bar Counter Area';
                $name = "Bar High Top {$i}";
                $capacity = 2;
                $notes = 'Bar counter seating for single diners & cocktails';
            }

            $status = 'VACANT';
            if ($i === 1 || $i === 2) {
                $status = 'OCCUPIED';
            } elseif ($i === 3) {
                $status = 'ORDERING';
            } elseif ($i === 4) {
                $status = 'BILLING';
            } elseif ($i === 37) {
                $status = 'RESERVED';
            }

            DiningTable::updateOrCreate(
                [
                    'restaurant_id' => $restaurant->id,
                    'table_number' => $tableNum,
                ],
                [
                    'name' => $name,
                    'seating_capacity' => $capacity,
                    'floor_area' => $area,
                    'status' => $status,
                    'notes' => $notes,
                    'is_active' => true,
                    'qr_token' => Str::random(32),
                ]
            );
        }

        // Active Orders for Table 1 and Table 2 (Table 2 is ready to change to Table 5!)
        $table1 = DiningTable::where('restaurant_id', $restaurant->id)->where('table_number', '1')->first();
        $table2 = DiningTable::where('restaurant_id', $restaurant->id)->where('table_number', '2')->first();
        $products = Product::where('restaurant_id', $restaurant->id)->get();

        if ($table1 && $products->count() >= 2 && ! Order::where('table_number', '1')->whereIn('status', ['PENDING', 'OCCUPIED'])->exists()) {
            $p1 = $products[0];
            $p2 = $products[1];
            $subtotal1 = ($p1->price * 2) + ($p2->price * 1);
            $tax1 = round($subtotal1 * 0.05);

            $order1 = Order::create([
                'restaurant_id' => $restaurant->id,
                'order_number' => 'ORD-'.date('Ymd').'-0001',
                'table_number' => '1',
                'status' => 'OCCUPIED',
                'kitchen_status' => 'PENDING_COOK',
                'subtotal' => $subtotal1,
                'tax_amount' => $tax1,
                'discount_amount' => 0,
                'total_amount' => $subtotal1 + $tax1,
                'reprint_count' => 0,
            ]);

            OrderItem::create([
                'order_id' => $order1->id,
                'product_id' => $p1->id,
                'category_id' => $p1->category_id,
                'item_name' => $p1->name,
                'quantity' => 2,
                'unit_price' => $p1->price,
                'cost_price' => $p1->cost_price ?? 0,
                'subtotal' => $p1->price * 2,
                'profit' => ($p1->price - ($p1->cost_price ?? 0)) * 2,
                'special_notes' => 'Less spicy, extra crisp',
                'is_cooked' => false,
                'is_verified' => false,
            ]);

            OrderItem::create([
                'order_id' => $order1->id,
                'product_id' => $p2->id,
                'category_id' => $p2->category_id,
                'item_name' => $p2->name,
                'quantity' => 1,
                'unit_price' => $p2->price,
                'cost_price' => $p2->cost_price ?? 0,
                'subtotal' => $p2->price * 1,
                'profit' => ($p2->price - ($p2->cost_price ?? 0)) * 1,
                'special_notes' => 'Cold with ice',
                'is_cooked' => false,
                'is_verified' => false,
            ]);

            $table1->update([
                'status' => 'OCCUPIED',
                'current_order_id' => $order1->id,
            ]);
        }

        if ($table2 && ! Order::where('table_number', '2')->whereIn('status', ['PENDING', 'OCCUPIED'])->exists()) {
            $p1 = $products->first();
            $subtotal2 = 19000;
            $tax2 = 950;
            $total2 = 19950;

            $order2 = Order::create([
                'restaurant_id' => $restaurant->id,
                'order_number' => 'ORD-'.date('Ymd').'-0002',
                'table_number' => '2',
                'status' => 'OCCUPIED',
                'kitchen_status' => 'READY_FOR_DELIVERY',
                'subtotal' => $subtotal2,
                'tax_amount' => $tax2,
                'discount_amount' => 0,
                'total_amount' => $total2,
                'reprint_count' => 0,
            ]);

            OrderItem::create([
                'order_id' => $order2->id,
                'product_id' => $p1 ? $p1->id : null,
                'category_id' => $p1 ? $p1->category_id : null,
                'item_name' => 'Beef Steak Special',
                'quantity' => 2,
                'unit_price' => 4500,
                'cost_price' => 2500,
                'subtotal' => 9000,
                'profit' => 4000,
                'special_notes' => 'Less spicy, extra crisp',
                'is_cooked' => true,
                'is_verified' => false,
            ]);

            OrderItem::create([
                'order_id' => $order2->id,
                'product_id' => $p1 ? $p1->id : null,
                'category_id' => $p1 ? $p1->category_id : null,
                'item_name' => 'Cabernet Red Wine Bottle',
                'quantity' => 1,
                'unit_price' => 7000,
                'cost_price' => 4000,
                'subtotal' => 7000,
                'profit' => 3000,
                'special_notes' => 'Cold with ice',
                'is_cooked' => true,
                'is_verified' => false,
            ]);

            OrderItem::create([
                'order_id' => $order2->id,
                'product_id' => $p1 ? $p1->id : null,
                'category_id' => $p1 ? $p1->category_id : null,
                'item_name' => 'Single Malt Scotch Shot',
                'quantity' => 1,
                'unit_price' => 3000,
                'cost_price' => 1500,
                'subtotal' => 3000,
                'profit' => 1500,
                'special_notes' => null,
                'is_cooked' => true,
                'is_verified' => false,
            ]);

            $table2->update([
                'status' => 'OCCUPIED',
                'current_order_id' => $order2->id,
            ]);
        }
    }
}
