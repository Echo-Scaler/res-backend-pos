<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Http\Requests\Order\StoreTableOrderRequest;
use App\Http\Resources\OrderSlipResource;
use App\Models\AuditLog;
use App\Models\DiningTable;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class CustomerOrderController extends Controller
{
    /**
     * Display the Mobile Customer Self-Ordering page for a dining table.
     */
    public function show(string $qr_token): View
    {
        $table = DiningTable::where('qr_token', $qr_token)
            ->where('is_active', true)
            ->firstOrFail();

        return view('order.table', compact('table'));
    }

    /**
     * Store an order placed by customer scanning table QR code.
     */
    public function store(StoreTableOrderRequest $request, string $qr_token): JsonResponse
    {
        $table = DiningTable::where('qr_token', $qr_token)
            ->where('is_active', true)
            ->firstOrFail();

        $restaurant = $table->restaurant;
        $itemsData = $request->validated('items');

        return DB::transaction(function () use ($table, $restaurant, $itemsData, $request) {
            $todayCount = Order::where('restaurant_id', $restaurant->id)
                ->whereDate('created_at', today())
                ->count();
            $orderNumber = 'ORD-'.now()->format('Ymd').'-'.str_pad($todayCount + 1, 4, '0', STR_PAD_LEFT);

            $subtotal = 0;
            $orderItemsToInsert = [];

            foreach ($itemsData as $item) {
                $product = Product::where('restaurant_id', $restaurant->id)
                    ->where('id', $item['product_id'])
                    ->firstOrFail();

                $qty = (int) $item['quantity'];
                $itemSubtotal = $product->price * $qty;
                $subtotal += $itemSubtotal;

                $costPrice = $product->cost_price ?? 0;
                $profit = ($product->price - $costPrice) * $qty;

                $orderItemsToInsert[] = [
                    'product_id' => $product->id,
                    'category_id' => $product->category_id,
                    'item_name' => $product->name,
                    'quantity' => $qty,
                    'unit_price' => $product->price,
                    'cost_price' => $costPrice,
                    'subtotal' => $itemSubtotal,
                    'profit' => $profit,
                    'special_notes' => $item['special_notes'] ?? null,
                    'is_cooked' => false,
                    'is_verified' => false,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }

            // Commercial Tax (5%) per Myanmar Revenue standard
            $taxAmount = round($subtotal * 0.05);
            $totalAmount = $subtotal + $taxAmount;

            $order = Order::create([
                'restaurant_id' => $restaurant->id,
                'order_number' => $orderNumber,
                'table_number' => $table->table_number,
                'status' => 'OCCUPIED',
                'kitchen_status' => 'PENDING_COOK',
                'subtotal' => $subtotal,
                'tax_amount' => $taxAmount,
                'discount_amount' => 0,
                'total_amount' => $totalAmount,
                'reprint_count' => 0,
            ]);

            foreach ($orderItemsToInsert as &$itemRow) {
                $itemRow['order_id'] = $order->id;
                OrderItem::create($itemRow);
            }

            // Update table status to OCCUPIED and bind current order
            $table->update([
                'status' => 'OCCUPIED',
                'current_order_id' => $order->id,
            ]);

            AuditLog::create([
                'restaurant_id' => $restaurant->id,
                'user_id' => null,
                'user_name' => "Customer (Table {$table->table_number})",
                'action' => 'CUSTOMER_SELF_ORDER',
                'description' => "Customer at Table {$table->table_number} placed Order #{$order->order_number} (".count($orderItemsToInsert)." dishes, {$totalAmount} MMK)",
                'ip_address' => $request->ip(),
            ]);

            return response()->json([
                'success' => true,
                'message' => "Order #{$order->order_number} submitted to kitchen for Table {$table->table_number}!",
                'order' => new OrderSlipResource($order->fresh(['items'])),
                'customer_bill_url' => route('admin.orders.printCustomerBill', $order),
                'kitchen_chit_url' => route('admin.orders.printKitchenChit', $order),
            ]);
        });
    }

    /**
     * Quick Simulation helper: Generate a realistic live active order for testing from Admin / Expediter Screen.
     */
    public function simulate(Request $request): JsonResponse|RedirectResponse
    {
        $user = $request->user();
        $restaurant = $user->restaurant;

        $targetTableNumber = $request->input('table_number');
        if ($targetTableNumber) {
            $table = DiningTable::where('restaurant_id', $restaurant->id)
                ->where('table_number', $targetTableNumber)
                ->first();
        } else {
            $table = DiningTable::where('restaurant_id', $restaurant->id)->first();
        }

        if (! $table) {
            return redirect()->back()->with('error', 'No dining tables found to simulate an order.');
        }

        $products = Product::where('restaurant_id', $restaurant->id)->where('is_available', true)->take(3)->get();
        if ($products->isEmpty()) {
            return redirect()->back()->with('error', 'No active products found in menu.');
        }

        $todayCount = Order::where('restaurant_id', $restaurant->id)
            ->whereDate('created_at', today())
            ->count();
        $orderNumber = 'ORD-'.now()->format('Ymd').'-'.str_pad($todayCount + 1, 4, '0', STR_PAD_LEFT);

        $order = DB::transaction(function () use ($table, $restaurant, $products, $orderNumber, $request, $user) {
            $subtotal = 0;
            $itemsData = [];

            foreach ($products as $index => $prod) {
                $qty = ($index === 0) ? 2 : 1;
                $lineSubtotal = $prod->price * $qty;
                $subtotal += $lineSubtotal;

                $notes = match ($index) {
                    0 => 'Less spicy, extra crisp',
                    1 => 'Cold with ice',
                    default => null,
                };

                $costPrice = $prod->cost_price ?? 0;
                $profit = ($prod->price - $costPrice) * $qty;

                $itemsData[] = [
                    'product_id' => $prod->id,
                    'category_id' => $prod->category_id,
                    'item_name' => $prod->name,
                    'quantity' => $qty,
                    'unit_price' => $prod->price,
                    'cost_price' => $costPrice,
                    'subtotal' => $lineSubtotal,
                    'profit' => $profit,
                    'special_notes' => $notes,
                    'is_cooked' => false,
                    'is_verified' => false,
                ];
            }

            $taxAmount = round($subtotal * 0.05);
            $totalAmount = $subtotal + $taxAmount;

            $order = Order::create([
                'restaurant_id' => $restaurant->id,
                'order_number' => $orderNumber,
                'table_number' => $table->table_number,
                'status' => 'OCCUPIED',
                'kitchen_status' => 'PENDING_COOK',
                'subtotal' => $subtotal,
                'tax_amount' => $taxAmount,
                'discount_amount' => 0,
                'total_amount' => $totalAmount,
                'reprint_count' => 0,
            ]);

            foreach ($itemsData as $itemRow) {
                $itemRow['order_id'] = $order->id;
                OrderItem::create($itemRow);
            }

            $table->update([
                'status' => 'OCCUPIED',
                'current_order_id' => $order->id,
            ]);

            AuditLog::create([
                'restaurant_id' => $restaurant->id,
                'user_id' => $user->id,
                'user_name' => $user->name,
                'action' => 'SIMULATE_TABLE_ORDER',
                'description' => "Staff {$user->name} created test active Order #{$order->order_number} for Table {$table->table_number}",
                'ip_address' => $request->ip(),
            ]);

            return $order;
        });

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => "Live active order generated for Table {$table->table_number}!",
                'order' => new OrderSlipResource($order->fresh(['items'])),
            ]);
        }

        return redirect()->route('admin.orders.index')->with('success', "Live active order generated for Table {$table->table_number}!");
    }
}
