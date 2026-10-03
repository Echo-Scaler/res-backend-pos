<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Http\Requests\Pos\SettleOrderRequest;
use App\Http\Resources\Pos\CashierOrderResource;
use App\Http\Resources\Pos\ReceiptResource;
use App\Models\AuditLog;
use App\Models\CashDrawerSession;
use App\Models\DiningTable;
use App\Models\Order;
use App\Models\Payment;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class CashierCheckoutController extends Controller
{
    /**
     * Display the Real Cashier Register Dashboard with active tables & drawer status.
     */
    public function index(Request $request): View
    {
        $user = $request->user();
        $restaurant = $user->restaurant;

        $activeSession = CashDrawerSession::where('restaurant_id', $restaurant->id)
            ->where('user_id', $user->id)
            ->where('status', 'OPEN')
            ->latest('id')
            ->first();

        // Get dining tables that are currently occupied or billing
        $activeTables = DiningTable::where('restaurant_id', $restaurant->id)
            ->whereIn('status', ['OCCUPIED', 'BILLING'])
            ->with(['currentOrder.items'])
            ->orderByRaw("CASE WHEN status = 'BILLING' THEN 1 ELSE 2 END")
            ->orderBy('table_number')
            ->get();

        // Today's completed transactions count and sales for register KPI
        $completedOrdersToday = Order::where('restaurant_id', $restaurant->id)
            ->whereDate('created_at', today())
            ->where('status', 'COMPLETED');

        $register = [
            'status' => $activeSession ? 'OPEN' : 'CLOSED',
            'terminal_id' => $activeSession ? $activeSession->terminal_code : 'POS-REG-01',
            'opening_balance' => $activeSession ? number_format($activeSession->opening_float, 0).' MMK' : '0 MMK',
            'current_sales' => $activeSession ? number_format($activeSession->cash_sales + $activeSession->digital_sales, 0).' MMK' : '0 MMK',
            'cash_sales' => $activeSession ? number_format($activeSession->cash_sales, 0).' MMK' : '0 MMK',
            'digital_sales' => $activeSession ? number_format($activeSession->digital_sales, 0).' MMK' : '0 MMK',
            'expected_cash' => $activeSession ? number_format($activeSession->expected_cash, 0).' MMK' : '0 MMK',
            'transactions_count' => $completedOrdersToday->count(),
            'pending_billing_count' => $activeTables->where('status', 'BILLING')->count(),
        ];

        $recentOrders = Order::where('restaurant_id', $restaurant->id)
            ->where('status', 'COMPLETED')
            ->latest('updated_at')
            ->take(5)
            ->get();

        return view('admin.roles.cashier', compact('user', 'restaurant', 'activeSession', 'activeTables', 'register', 'recentOrders'));
    }

    /**
     * Get JSON order details for checkout modal calculation.
     */
    public function getOrderDetails(Order $order, Request $request): JsonResponse
    {
        $user = $request->user();
        if ($order->restaurant_id !== $user->restaurant_id) {
            return response()->json(['success' => false, 'message' => 'Unauthorized access.'], 403);
        }

        $order->load(['items', 'restaurant']);

        return response()->json([
            'success' => true,
            'order' => new CashierOrderResource($order),
        ]);
    }

    /**
     * Settle active table bill with Cash / KBZPay / WavePay / Card.
     */
    public function settleOrder(SettleOrderRequest $request, Order $order): JsonResponse|RedirectResponse
    {
        $user = $request->user();
        $restaurant = $user->restaurant;

        if ($order->restaurant_id !== $restaurant->id) {
            abort(403, 'Unauthorized order settlement.');
        }

        if ($order->status === 'COMPLETED' || $order->payment_status === 'PAID') {
            $msg = 'ဤအော်ဒါသည် ငွေရှင်းပြီးစီးပြီး ဖြစ်ပါသည်။';
            if ($request->wantsJson()) {
                return response()->json(['success' => false, 'message' => $msg], 422);
            }

            return redirect()->back()->with('error', $msg);
        }

        $activeSession = CashDrawerSession::where('restaurant_id', $restaurant->id)
            ->where('user_id', $user->id)
            ->where('status', 'OPEN')
            ->first();

        return DB::transaction(function () use ($request, $order, $restaurant, $user, $activeSession) {
            // Calculate Discount
            $discountType = $request->input('discount_type', 'NONE');
            $discountValue = (float) $request->input('discount_value', 0);
            $discountAmount = 0;

            if ($discountType === 'PERCENT' && $discountValue > 0) {
                $discountAmount = (int) round($order->subtotal * ($discountValue / 100));
            } elseif ($discountType === 'FIXED' && $discountValue > 0) {
                $discountAmount = (int) min($order->subtotal, $discountValue);
            }

            $taxableSubtotal = max(0, $order->subtotal - $discountAmount);
            // 5% Commercial Tax
            $taxAmount = (int) round($taxableSubtotal * 0.05);
            $totalDue = $taxableSubtotal + $taxAmount;

            $method = $request->input('payment_method');
            $tendered = (int) $request->input('amount_tendered', $totalDue);
            $changeAmount = 0;

            if ($method === 'CASH') {
                if ($tendered < $totalDue) {
                    throw new \InvalidArgumentException('ပေးချေငွေ ('.number_format($tendered, 0).' MMK) သည် ကျသင့်ငွေ ('.number_format($totalDue, 0).' MMK) ထက် နည်းနေပါသည်။');
                }
                $changeAmount = $tendered - $totalDue;

                Payment::create([
                    'restaurant_id' => $restaurant->id,
                    'order_id' => $order->id,
                    'cash_drawer_session_id' => $activeSession?->id,
                    'payment_method' => 'CASH',
                    'amount' => $totalDue,
                    'tendered_amount' => $tendered,
                    'change_amount' => $changeAmount,
                    'status' => 'SUCCESS',
                    'reference_no' => 'CASH-'.now()->format('His'),
                ]);
            } elseif ($method === 'SPLIT') {
                $cashPart = (int) $request->input('split_cash_amount', 0);
                $digitalPart = (int) $request->input('split_digital_amount', 0);
                $digitalMethod = $request->input('split_digital_method', 'KBZPAY');
                $digitalRef = $request->input('split_digital_ref');

                if (($cashPart + $digitalPart) < $totalDue) {
                    throw new \InvalidArgumentException('Split ငွေပေးချေမှု ပေါင်းလဒ်သည် စုစုပေါင်း ကျသင့်ငွေနှင့် မပြည့်မီပါ။');
                }

                if ($cashPart > 0) {
                    Payment::create([
                        'restaurant_id' => $restaurant->id,
                        'order_id' => $order->id,
                        'cash_drawer_session_id' => $activeSession?->id,
                        'payment_method' => 'CASH',
                        'amount' => $cashPart,
                        'tendered_amount' => $cashPart,
                        'change_amount' => 0,
                        'status' => 'SUCCESS',
                        'reference_no' => 'SPLIT-CASH-'.now()->format('His'),
                    ]);
                }

                if ($digitalPart > 0) {
                    Payment::create([
                        'restaurant_id' => $restaurant->id,
                        'order_id' => $order->id,
                        'cash_drawer_session_id' => $activeSession?->id,
                        'payment_method' => $digitalMethod,
                        'amount' => $digitalPart,
                        'tendered_amount' => $digitalPart,
                        'change_amount' => 0,
                        'status' => 'SUCCESS',
                        'reference_no' => $digitalRef ?? $digitalMethod.'-'.now()->format('His'),
                    ]);
                }
            } else {
                // Digital single payment (KBZPAY, WAVEPAY, CARD)
                Payment::create([
                    'restaurant_id' => $restaurant->id,
                    'order_id' => $order->id,
                    'cash_drawer_session_id' => $activeSession?->id,
                    'payment_method' => $method,
                    'amount' => $totalDue,
                    'tendered_amount' => $totalDue,
                    'change_amount' => 0,
                    'status' => 'SUCCESS',
                    'reference_no' => $request->input('reference_no') ?? $method.'-'.now()->format('His'),
                ]);
            }

            // Update Order
            $order->update([
                'discount_amount' => $discountAmount,
                'tax_amount' => $taxAmount,
                'total_amount' => $totalDue,
                'paid_amount' => $totalDue,
                'status' => 'COMPLETED',
                'payment_status' => 'PAID',
                'cash_drawer_session_id' => $activeSession?->id,
            ]);

            // Release Dining Table to VACANT
            $table = DiningTable::where('restaurant_id', $restaurant->id)
                ->where('table_number', $order->table_number)
                ->first();

            if ($table) {
                $table->update([
                    'status' => 'VACANT',
                    'current_order_id' => null,
                ]);
            }

            // Recalculate Cashier Session
            $activeSession?->recalculate();

            AuditLog::create([
                'restaurant_id' => $restaurant->id,
                'user_id' => $user->id,
                'user_name' => $user->name,
                'action' => 'SETTLE_ORDER_BILL',
                'description' => "Cashier {$user->name} settled Order #{$order->order_number} for Table {$order->table_number} (".number_format($totalDue, 0)." MMK via {$method})",
                'ip_address' => $request->ip(),
            ]);

            $receiptUrl = route('cashier.orders.receipt', $order);

            if ($request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => "Order #{$order->order_number} ငွေရှင်းပြီးစီးပါပြီ။ ပြန်အမ်းငွေ: ".number_format($changeAmount, 0).' MMK',
                    'receipt_url' => $receiptUrl,
                    'order' => new ReceiptResource($order->fresh(['items', 'restaurant', 'payments', 'cashDrawerSession.cashier'])),
                ]);
            }

            return redirect()->route('cashier.orders.receipt', $order)
                ->with('success', "Order #{$order->order_number} ငွေရှင်းပြီးစီးပါပြီ။");
        });
    }

    /**
     * Print Official 80mm Thermal Receipt (Tax Invoice & Bill).
     */
    public function printReceipt(Order $order, Request $request): View
    {
        $user = $request->user();
        if ($order->restaurant_id !== $user->restaurant_id) {
            abort(403, 'Unauthorized receipt access.');
        }

        $order->load(['items', 'restaurant', 'payments', 'staff', 'cashDrawerSession.cashier']);

        return view('admin.orders.print-receipt', compact('order'));
    }
}
