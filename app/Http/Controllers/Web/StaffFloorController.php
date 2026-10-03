<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Http\Requests\Pos\AddOrderItemsRequest;
use App\Http\Requests\Pos\RequestBillRequest;
use App\Http\Requests\Pos\StoreTablesideOrderRequest;
use App\Http\Resources\OrderSlipResource;
use App\Http\Resources\Pos\DiningTablePosResource;
use App\Models\AuditLog;
use App\Models\Category;
use App\Models\DiningTable;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class StaffFloorController extends Controller
{
    /**
     * Display the Floor Staff / Waiter Tableside Ordering Portal.
     */
    public function index(Request $request): View
    {
        $user = $request->user();
        $restaurant = $user->restaurant;

        $tables = DiningTable::where('restaurant_id', $restaurant->id)
            ->where('is_active', true)
            ->with(['currentOrder.items'])
            ->orderBy('floor_area')
            ->orderBy('table_number')
            ->get();

        $categories = Category::where('restaurant_id', $restaurant->id)
            ->with(['products' => function ($q) {
                $q->where('is_available', true);
            }])
            ->get();

        $products = Product::where('restaurant_id', $restaurant->id)
            ->where('is_available', true)
            ->get();

        $stats = [
            'active_tables' => $tables->where('status', 'OCCUPIED')->count(),
            'billing_tables' => $tables->where('status', 'BILLING')->count(),
            'vacant_tables' => $tables->where('status', 'VACANT')->count(),
            'ready_pickup_orders' => Order::where('restaurant_id', $restaurant->id)
                ->where('kitchen_status', 'READY_FOR_DELIVERY')
                ->whereDate('created_at', today())
                ->count(),
        ];

        return view('admin.roles.staff', compact('user', 'restaurant', 'tables', 'categories', 'products', 'stats'));
    }

    /**
     * Store a new tableside order taken by a waiter.
     */
    public function storeOrder(StoreTablesideOrderRequest $request): JsonResponse|RedirectResponse
    {
        $user = $request->user();
        $restaurant = $user->restaurant;

        $table = DiningTable::where('restaurant_id', $restaurant->id)
            ->where('id', $request->input('table_id'))
            ->firstOrFail();

        $itemsData = $request->validated('items');

        return DB::transaction(function () use ($table, $restaurant, $itemsData, $request, $user) {
            $todayCount = Order::where('restaurant_id', $restaurant->id)
                ->whereDate('created_at', today())
                ->count();
            $orderNumber = 'ORD-'.now()->format('Ymd').'-'.str_pad($todayCount + 1, 4, '0', STR_PAD_LEFT);

            $subtotal = 0;
            $itemsToInsert = [];

            foreach ($itemsData as $item) {
                $product = Product::where('restaurant_id', $restaurant->id)
                    ->where('id', $item['product_id'])
                    ->firstOrFail();

                $qty = (int) $item['quantity'];
                $lineSubtotal = $product->price * $qty;
                $subtotal += $lineSubtotal;

                $costPrice = $product->cost_price ?? 0;
                $profit = ($product->price - $costPrice) * $qty;

                $itemsToInsert[] = [
                    'product_id' => $product->id,
                    'category_id' => $product->category_id,
                    'item_name' => $product->name,
                    'quantity' => $qty,
                    'unit_price' => $product->price,
                    'cost_price' => $costPrice,
                    'subtotal' => $lineSubtotal,
                    'profit' => $profit,
                    'special_notes' => $item['special_notes'] ?? null,
                    'is_cooked' => false,
                    'is_verified' => false,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }

            // Commercial Tax (5%)
            $taxAmount = (int) round($subtotal * 0.05);
            $totalAmount = $subtotal + $taxAmount;

            $order = Order::create([
                'restaurant_id' => $restaurant->id,
                'order_number' => $orderNumber,
                'table_number' => $table->table_number,
                'guest_count' => (int) $request->input('guest_count', 1),
                'order_type' => 'DINE_IN',
                'status' => 'OCCUPIED',
                'kitchen_status' => 'PENDING_COOK',
                'payment_status' => 'UNPAID',
                'subtotal' => $subtotal,
                'discount_amount' => 0,
                'tax_amount' => $taxAmount,
                'total_amount' => $totalAmount,
                'reprint_count' => 0,
                'staff_id' => $user->id,
            ]);

            foreach ($itemsToInsert as &$row) {
                $row['order_id'] = $order->id;
                OrderItem::create($row);
            }

            $table->update([
                'status' => 'OCCUPIED',
                'current_order_id' => $order->id,
            ]);

            AuditLog::create([
                'restaurant_id' => $restaurant->id,
                'user_id' => $user->id,
                'user_name' => $user->name,
                'action' => 'WAITER_TABLE_ORDER',
                'description' => "Waiter {$user->name} placed Order #{$order->order_number} for Table {$table->table_number} (".count($itemsToInsert)." dishes, {$totalAmount} MMK)",
                'ip_address' => $request->ip(),
            ]);

            if ($request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => "Order #{$order->order_number} submitted to kitchen for Table {$table->table_number}!",
                    'order' => new OrderSlipResource($order->fresh(['items'])),
                    'table' => new DiningTablePosResource($table->fresh()),
                    'kitchen_chit_url' => route('admin.orders.printKitchenChit', $order),
                ]);
            }

            return redirect()->route('staff.dashboard')->with('success', "Order #{$order->order_number} sent to kitchen for Table {$table->table_number}!");
        });
    }

    /**
     * Add repeat rounds / extra dishes to an existing occupied table order.
     */
    public function addItems(AddOrderItemsRequest $request, Order $order): JsonResponse|RedirectResponse
    {
        $user = $request->user();
        $restaurant = $user->restaurant;

        if ($order->restaurant_id !== $restaurant->id) {
            abort(403, 'Unauthorized order modification.');
        }

        if ($order->status === 'COMPLETED' || $order->status === 'CANCELLED') {
            $msg = 'ပြီးစီးသွားသော အော်ဒါသို့ ဟင်းလျာ ထပ်ပေါင်း၍ မရပါ။';
            if ($request->wantsJson()) {
                return response()->json(['success' => false, 'message' => $msg], 422);
            }

            return redirect()->back()->with('error', $msg);
        }

        $itemsData = $request->validated('items');

        return DB::transaction(function () use ($order, $restaurant, $itemsData, $request, $user) {
            $addedSubtotal = 0;

            foreach ($itemsData as $item) {
                $product = Product::where('restaurant_id', $restaurant->id)
                    ->where('id', $item['product_id'])
                    ->firstOrFail();

                $qty = (int) $item['quantity'];
                $lineSubtotal = $product->price * $qty;
                $addedSubtotal += $lineSubtotal;

                $costPrice = $product->cost_price ?? 0;
                $profit = ($product->price - $costPrice) * $qty;

                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $product->id,
                    'category_id' => $product->category_id,
                    'item_name' => $product->name,
                    'quantity' => $qty,
                    'unit_price' => $product->price,
                    'cost_price' => $costPrice,
                    'subtotal' => $lineSubtotal,
                    'profit' => $profit,
                    'special_notes' => $item['special_notes'] ?? null,
                    'is_cooked' => false,
                    'is_verified' => false,
                ]);
            }

            $newSubtotal = $order->subtotal + $addedSubtotal;
            $newTax = (int) round(($newSubtotal - $order->discount_amount) * 0.05);
            $newTotal = ($newSubtotal - $order->discount_amount) + $newTax;

            $order->update([
                'subtotal' => $newSubtotal,
                'tax_amount' => $newTax,
                'total_amount' => $newTotal,
                'kitchen_status' => 'PENDING_COOK', // reset kitchen cook status for new dishes
            ]);

            AuditLog::create([
                'restaurant_id' => $restaurant->id,
                'user_id' => $user->id,
                'user_name' => $user->name,
                'action' => 'ADD_ORDER_ROUND',
                'description' => "Waiter {$user->name} added ".count($itemsData)." more items to Order #{$order->order_number} (+{$addedSubtotal} MMK)",
                'ip_address' => $request->ip(),
            ]);

            if ($request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => "Added items to Order #{$order->order_number} successfully!",
                    'order' => new OrderSlipResource($order->fresh(['items'])),
                ]);
            }

            return redirect()->route('staff.dashboard')->with('success', "Added items to Order #{$order->order_number}!");
        });
    }

    /**
     * Mark table status as BILLING (Requested Bill from table).
     */
    public function requestBill(RequestBillRequest $request): JsonResponse|RedirectResponse
    {
        $user = $request->user();
        $restaurant = $user->restaurant;

        $table = DiningTable::where('restaurant_id', $restaurant->id)
            ->where('id', $request->input('table_id'))
            ->firstOrFail();

        if ($table->status === 'VACANT') {
            $msg = 'ဧည့်သည် မရှိသော စားပွဲအတွက် ငွေတောင်းခံ၍ မရပါ။';
            if ($request->wantsJson()) {
                return response()->json(['success' => false, 'message' => $msg], 422);
            }

            return redirect()->back()->with('error', $msg);
        }

        $table->update(['status' => 'BILLING']);

        AuditLog::create([
            'restaurant_id' => $restaurant->id,
            'user_id' => $user->id,
            'user_name' => $user->name,
            'action' => 'REQUEST_TABLE_BILL',
            'description' => "Staff {$user->name} requested bill for Table {$table->table_number}",
            'ip_address' => $request->ip(),
        ]);

        $msg = "Table {$table->table_number} ငွေရှင်းရန် တောင်းဆိုမှု ကောင်တာသို့ အကြောင်းကြားပြီးပါပြီ။";

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => $msg,
                'table' => new DiningTablePosResource($table),
            ]);
        }

        return redirect()->route('staff.dashboard')->with('success', $msg);
    }
}
