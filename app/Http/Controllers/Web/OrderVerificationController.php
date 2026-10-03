<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Order\ReprintSlipRequest;
use App\Http\Requests\Admin\Order\VerifyOrderItemsRequest;
use App\Http\Resources\OrderSlipResource;
use App\Models\AuditLog;
use App\Models\DiningTable;
use App\Models\Order;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\View\View;

class OrderVerificationController extends Controller
{
    /**
     * Display the Restaurant Expediter & Order Verification Pass Screen.
     */
    public function index(Request $request): View|JsonResponse
    {
        $user = $request->user();
        $restaurant = $user->restaurant;

        $viewFilter = $request->query('view', 'active');
        $statusFilter = $request->query('status');
        $tableFilter = $request->query('table');

        $query = $restaurant->orders()
            ->with(['items', 'verifier'])
            ->orderByDesc('id');

        if ($viewFilter !== 'all') {
            $query->whereIn('status', ['PENDING', 'OCCUPIED']);
        }

        if ($statusFilter) {
            $query->where('kitchen_status', $statusFilter);
        }

        if ($tableFilter) {
            $query->where('table_number', $tableFilter);
        }

        $orders = $query->get();

        // Calculate expediter metrics
        $allActive = $restaurant->orders()->whereIn('status', ['PENDING', 'OCCUPIED'])->get();
        $metrics = [
            'total_active' => $allActive->count(),
            'pending_cook' => $allActive->where('kitchen_status', 'PENDING_COOK')->count(),
            'cooking' => $allActive->where('kitchen_status', 'COOKING')->count(),
            'ready_for_delivery' => $allActive->where('kitchen_status', 'READY_FOR_DELIVERY')->count(),
            'served' => $allActive->where('kitchen_status', 'SERVED_TO_TABLE')->count(),
        ];

        $tables = $restaurant->diningTables()->active()->pluck('table_number')->values();

        if ($request->wantsJson()) {
            return response()->json([
                'metrics' => $metrics,
                'orders' => OrderSlipResource::collection($orders),
                'tables' => $tables,
            ]);
        }

        return view('admin.orders.verification', compact('orders', 'metrics', 'tables', 'statusFilter', 'tableFilter', 'viewFilter', 'restaurant'));
    }

    /**
     * Verify items before carrying to table or toggle item readiness.
     */
    public function verify(VerifyOrderItemsRequest $request, Order $order): JsonResponse|RedirectResponse
    {
        $this->authorizeOrder($request, $order);

        $action = $request->validated('action');
        $user = $request->user();

        if ($action === 'TOGGLE_ITEM') {
            $itemId = $request->validated('item_id');
            $item = $order->items()->findOrFail($itemId);
            $item->is_verified = ! $item->is_verified;
            $item->save();

            $message = "Item '{$item->item_name}' verification toggled.";
        } elseif ($action === 'MARK_READY') {
            $order->kitchen_status = 'READY_FOR_DELIVERY';
            $order->items()->update(['is_cooked' => true]);
            $order->save();

            $message = "Order #{$order->order_number} marked ready for waiter pickup!";
        } else {
            // VERIFY_AND_DELIVER (Carry to table)
            $order->items()->update(['is_verified' => true, 'is_cooked' => true]);
            $order->kitchen_status = 'SERVED_TO_TABLE';
            $order->status = 'OCCUPIED';
            $order->verified_at = Carbon::now();
            $order->verified_by = $user->id;
            $order->save();

            // Also ensure table status is updated to OCCUPIED
            if ($order->table_number) {
                DiningTable::where('restaurant_id', $order->restaurant_id)
                    ->where('table_number', $order->table_number)
                    ->update([
                        'status' => 'OCCUPIED',
                        'current_order_id' => $order->id,
                    ]);
            }

            AuditLog::create([
                'restaurant_id' => $order->restaurant_id,
                'user_id' => $user->id,
                'user_name' => $user->name,
                'action' => 'VERIFIED_AND_SERVED_ORDER',
                'description' => "Waiter {$user->name} verified and delivered Order #{$order->order_number} to Table {$order->table_number}",
                'ip_address' => $request->ip(),
            ]);

            $message = "Order #{$order->order_number} verified and confirmed delivered to Table {$order->table_number}!";
        }

        if ($request->wantsJson()) {
            return response()->json([
                'message' => $message,
                'order' => new OrderSlipResource($order->fresh(['items', 'verifier'])),
            ]);
        }

        return redirect()->back()->with('success', $message);
    }

    /**
     * Reproduce / Reprint order slips with duplicate watermark.
     */
    public function reprint(ReprintSlipRequest $request, Order $order): JsonResponse|RedirectResponse
    {
        $this->authorizeOrder($request, $order);

        $slipType = $request->validated('slip_type');
        $reason = $request->validated('reason') ?? 'Staff requested reprint at station';
        $user = $request->user();

        $count = $order->incrementReprintCount();

        AuditLog::create([
            'restaurant_id' => $order->restaurant_id,
            'user_id' => $user->id,
            'user_name' => $user->name,
            'action' => 'REPRINT_ORDER_SLIP',
            'description' => "Reprinted {$slipType} for Order #{$order->order_number} (Reprint #{$count}). Reason: {$reason}",
            'ip_address' => $request->ip(),
        ]);

        $kitchenUrl = route('admin.orders.printKitchenChit', $order);
        $billUrl = route('admin.orders.printCustomerBill', $order);
        $bothUrl = route('admin.orders.printBothSlips', $order);

        $targetUrl = match ($slipType) {
            'KITCHEN_CHIT' => $kitchenUrl,
            'BOTH' => $bothUrl,
            default => $billUrl,
        };

        if ($request->wantsJson()) {
            return response()->json([
                'message' => "Order #{$order->order_number} slip re-issued successfully (Reprint #{$count}).",
                'reprint_count' => $count,
                'kitchen_chit_url' => $kitchenUrl,
                'customer_bill_url' => $billUrl,
                'both_slips_url' => $bothUrl,
                'target_url' => $targetUrl,
            ]);
        }

        return redirect($targetUrl);
    }

    /**
     * Render 80mm Continuous Dual Slips (Slip 1 Kitchen Chit + Slip 2 Customer Bill with tear line).
     */
    public function printBothSlips(Order $order, Request $request): View
    {
        $this->authorizeOrder($request, $order);
        $restaurant = $request->user()->restaurant;

        return view('admin.orders.print-both-slips', compact('order', 'restaurant'));
    }

    /**
     * Render 80mm Thermal Kitchen Order Chit (Slip 1 - 調理指示伝票).
     */
    public function printKitchenChit(Order $order, Request $request): View
    {
        $this->authorizeOrder($request, $order);
        $restaurant = $request->user()->restaurant;

        return view('admin.orders.print-kitchen-chit', compact('order', 'restaurant'));
    }

    /**
     * Render 80mm Thermal Customer Bill Slip / Guest Check (Slip 2 - 会計伝票).
     */
    public function printCustomerBill(Order $order, Request $request): View
    {
        $this->authorizeOrder($request, $order);
        $restaurant = $request->user()->restaurant;

        return view('admin.orders.print-customer-bill', compact('order', 'restaurant'));
    }

    /**
     * Authorize that order belongs to current user's restaurant.
     */
    private function authorizeOrder(Request $request, Order $order): void
    {
        if ($order->restaurant_id !== $request->user()->restaurant_id) {
            abort(403, 'Unauthorized access to this restaurant order.');
        }
    }
}
