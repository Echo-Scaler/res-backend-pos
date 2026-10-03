<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Http\Requests\Pos\CashInOutRequest;
use App\Http\Requests\Pos\CloseShiftRequest;
use App\Http\Requests\Pos\OpenShiftRequest;
use App\Http\Resources\Pos\CashDrawerSessionResource;
use App\Http\Resources\Pos\CashDrawerTransactionResource;
use App\Models\AuditLog;
use App\Models\CashDrawerSession;
use App\Models\CashDrawerTransaction;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CashDrawerController extends Controller
{
    /**
     * Open a new Cash Drawer Shift Session.
     */
    public function openShift(OpenShiftRequest $request): JsonResponse|RedirectResponse
    {
        $user = $request->user();
        $restaurant = $user->restaurant;

        $existingSession = CashDrawerSession::where('restaurant_id', $restaurant->id)
            ->where('user_id', $user->id)
            ->where('status', 'OPEN')
            ->first();

        if ($existingSession) {
            $msg = 'သင့်ထံတွင် ဖွင့်လှစ်ထားဆဲ Cashier Shift ('.$existingSession->terminal_code.') ရှိနေပါသည်။';
            if ($request->wantsJson()) {
                return response()->json(['success' => false, 'message' => $msg], 422);
            }

            return redirect()->back()->with('error', $msg);
        }

        $session = CashDrawerSession::create([
            'restaurant_id' => $restaurant->id,
            'user_id' => $user->id,
            'terminal_code' => $request->input('terminal_code', 'POS-REG-01'),
            'opened_at' => now(),
            'opening_float' => (int) $request->input('opening_float', 0),
            'cash_sales' => 0,
            'digital_sales' => 0,
            'cash_in' => 0,
            'cash_out' => 0,
            'expected_cash' => (int) $request->input('opening_float', 0),
            'status' => 'OPEN',
            'notes' => $request->input('notes'),
        ]);

        AuditLog::create([
            'restaurant_id' => $restaurant->id,
            'user_id' => $user->id,
            'user_name' => $user->name,
            'action' => 'OPEN_CASH_DRAWER_SHIFT',
            'description' => "Cashier {$user->name} opened shift ({$session->terminal_code}) with float ".number_format($session->opening_float, 0).' MMK',
            'ip_address' => $request->ip(),
        ]);

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Cashier Shift အောင်မြင်စွာ ဖွင့်လှစ်ပြီးပါပြီ။',
                'data' => new CashDrawerSessionResource($session),
            ]);
        }

        return redirect()->route('cashier.dashboard')->with('success', 'Cashier Shift အောင်မြင်စွာ ဖွင့်လှစ်ပြီးပါပြီ။');
    }

    /**
     * Record Cash In (Paid In) or Cash Out (Paid Out / Drop).
     */
    public function cashInOut(CashInOutRequest $request): JsonResponse|RedirectResponse
    {
        $user = $request->user();
        $restaurant = $user->restaurant;

        $session = CashDrawerSession::where('restaurant_id', $restaurant->id)
            ->where('user_id', $user->id)
            ->where('status', 'OPEN')
            ->first();

        if (! $session) {
            $msg = 'ဖွင့်လှစ်ထားသော Cashier Shift မရှိသေးပါ။';
            if ($request->wantsJson()) {
                return response()->json(['success' => false, 'message' => $msg], 422);
            }

            return redirect()->back()->with('error', $msg);
        }

        $type = $request->input('type');
        $amount = (int) $request->input('amount');
        $reason = $request->input('reason');

        if ($type === 'CASH_OUT' && $session->expected_cash < $amount) {
            $msg = 'အံဆွဲထဲတွင် ငွေသား မလုံလောက်ပါ (လက်ကျန်: '.number_format($session->expected_cash, 0).' MMK)';
            if ($request->wantsJson()) {
                return response()->json(['success' => false, 'message' => $msg], 422);
            }

            return redirect()->back()->with('error', $msg);
        }

        $tx = CashDrawerTransaction::create([
            'cash_drawer_session_id' => $session->id,
            'restaurant_id' => $restaurant->id,
            'user_id' => $user->id,
            'type' => $type,
            'amount' => $amount,
            'reason' => $reason,
        ]);

        $session->recalculate();

        AuditLog::create([
            'restaurant_id' => $restaurant->id,
            'user_id' => $user->id,
            'user_name' => $user->name,
            'action' => $type === 'CASH_IN' ? 'DRAWER_CASH_IN' : 'DRAWER_CASH_OUT',
            'description' => "Cashier {$user->name} recorded {$type} of ".number_format($amount, 0)." MMK ({$reason})",
            'ip_address' => $request->ip(),
        ]);

        $successMsg = $type === 'CASH_IN'
            ? 'ငွေသားထည့်သွင်းမှု (Cash In) အောင်မြင်ပါသည်။'
            : 'ငွေသားထုတ်ယူမှု (Cash Out) အောင်မြင်ပါသည်။';

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => $successMsg,
                'transaction' => new CashDrawerTransactionResource($tx),
                'session' => new CashDrawerSessionResource($session->fresh()),
            ]);
        }

        return redirect()->route('cashier.dashboard')->with('success', $successMsg);
    }

    /**
     * Close the active shift session and produce Z-Report reconciliation.
     */
    public function closeShift(CloseShiftRequest $request): JsonResponse|RedirectResponse
    {
        $user = $request->user();
        $restaurant = $user->restaurant;

        $session = CashDrawerSession::where('restaurant_id', $restaurant->id)
            ->where('user_id', $user->id)
            ->where('status', 'OPEN')
            ->first();

        if (! $session) {
            $msg = 'ပိတ်သိမ်းရန် ဖွင့်လှစ်ထားသော Cashier Shift မရှိပါ။';
            if ($request->wantsJson()) {
                return response()->json(['success' => false, 'message' => $msg], 422);
            }

            return redirect()->back()->with('error', $msg);
        }

        $session->recalculate();

        $actualCash = (int) $request->input('closing_actual_cash');
        $difference = $actualCash - $session->expected_cash;

        $session->update([
            'closing_actual_cash' => $actualCash,
            'cash_difference' => $difference,
            'status' => 'CLOSED',
            'closed_at' => now(),
            'notes' => $request->input('notes'),
        ]);

        AuditLog::create([
            'restaurant_id' => $restaurant->id,
            'user_id' => $user->id,
            'user_name' => $user->name,
            'action' => 'CLOSE_CASH_DRAWER_SHIFT',
            'description' => "Cashier {$user->name} closed shift ({$session->terminal_code}). Expected: ".number_format($session->expected_cash, 0).' MMK, Actual: '.number_format($actualCash, 0).' MMK, Diff: '.number_format($difference, 0).' MMK',
            'ip_address' => $request->ip(),
        ]);

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Cashier Shift အောင်မြင်စွာ ပိတ်သိမ်းပြီး နေ့ချုပ်စာရင်း (Z-Report) ထုတ်ယူနိုင်ပါပြီ။',
                'session' => new CashDrawerSessionResource($session),
                'z_report_url' => route('cashier.shift.zReport', $session),
            ]);
        }

        return redirect()->route('cashier.shift.zReport', $session)->with('success', 'Cashier Shift အောင်မြင်စွာ ပိတ်သိမ်းပြီး နေ့ချုပ်စာရင်း (Z-Report) ထွက်ရှိပါပြီ။');
    }

    /**
     * Display printable 80mm Z-Report (End of Shift Summary).
     */
    public function printZReport(CashDrawerSession $session, Request $request): View
    {
        $user = $request->user();
        if ($session->restaurant_id !== $user->restaurant_id) {
            abort(403, 'Unauthorized access to this session report.');
        }

        $session->load(['cashier', 'transactions', 'restaurant']);

        return view('admin.roles.z-report', compact('session'));
    }
}
