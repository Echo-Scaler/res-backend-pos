<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Promotions\StorePromotionRequest;
use App\Http\Requests\Admin\Promotions\UpdatePromotionRequest;
use App\Http\Resources\PromotionResource;
use App\Models\Promotion;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PromotionController extends Controller
{
    /**
     * Display discounts and coupon campaigns workspace.
     */
    public function index(Request $request): View
    {
        $currentUser = $request->user();
        $restaurant = $currentUser->restaurant;

        $query = Promotion::query()
            ->where('restaurant_id', $currentUser->restaurant_id);

        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('code', 'like', "%{$search}%")
                    ->orWhere('name', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            });
        }

        $promotions = $query->latest()->paginate(10)->withQueryString();

        $allPromos = Promotion::where('restaurant_id', $currentUser->restaurant_id)->get();

        $stats = [
            'total_promotions' => $allPromos->count(),
            'active_promotions' => $allPromos->filter(fn (Promotion $p) => $p->isValidNow())->count(),
            'percentage_coupons' => $allPromos->where('type', 'PERCENTAGE')->count(),
            'fixed_coupons' => $allPromos->where('type', 'FIXED')->count(),
        ];

        return view('admin.promotions.index', [
            'restaurant' => $restaurant,
            'currentUser' => $currentUser,
            'promotions' => $promotions,
            'stats' => $stats,
            'search' => $search,
        ]);
    }

    /**
     * Store a newly created coupon/promotion.
     */
    public function store(StorePromotionRequest $request): JsonResponse|RedirectResponse
    {
        $currentUser = $request->user();
        $validated = $request->validated();

        $promotion = Promotion::create([
            'restaurant_id' => $currentUser->restaurant_id,
            'code' => $validated['code'],
            'name' => $validated['name'],
            'description' => $validated['description'] ?? null,
            'type' => $validated['type'],
            'value' => $validated['value'],
            'min_order_amount' => $validated['min_order_amount'] ?? 0,
            'max_discount_amount' => $validated['max_discount_amount'] ?? null,
            'start_date' => $validated['start_date'] ?? null,
            'end_date' => $validated['end_date'] ?? null,
            'usage_limit' => $validated['usage_limit'] ?? null,
            'is_active' => $validated['is_active'] ?? true,
        ]);

        $message = "Promotion coupon '{$promotion->code}' created successfully.";

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => $message,
                'promotion' => new PromotionResource($promotion),
            ]);
        }

        return redirect()->route('admin.promotions.index')->with('success', $message);
    }

    /**
     * Update a promotion.
     */
    public function update(UpdatePromotionRequest $request, Promotion $promotion): JsonResponse|RedirectResponse
    {
        $currentUser = $request->user();

        if ($promotion->restaurant_id !== $currentUser->restaurant_id) {
            abort(403, 'Unauthorized access.');
        }

        $validated = $request->validated();
        $promotion->update($validated);

        $message = "Coupon '{$promotion->code}' updated successfully.";

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => $message,
                'promotion' => new PromotionResource($promotion),
            ]);
        }

        return redirect()->route('admin.promotions.index')->with('success', $message);
    }

    /**
     * Toggle promotion active status.
     */
    public function toggleActive(Request $request, Promotion $promotion): JsonResponse|RedirectResponse
    {
        $currentUser = $request->user();

        if ($promotion->restaurant_id !== $currentUser->restaurant_id) {
            abort(403, 'Unauthorized access.');
        }

        $promotion->update([
            'is_active' => ! $promotion->is_active,
        ]);

        $statusText = $promotion->is_active ? 'Activated' : 'Paused';
        $message = "Coupon '{$promotion->code}' is now {$statusText}.";

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => $message,
                'is_active' => $promotion->is_active,
                'promotion' => new PromotionResource($promotion),
            ]);
        }

        return back()->with('success', $message);
    }

    /**
     * Delete a promotion.
     */
    public function destroy(Request $request, Promotion $promotion): RedirectResponse
    {
        $currentUser = $request->user();

        if ($promotion->restaurant_id !== $currentUser->restaurant_id) {
            abort(403, 'Unauthorized access.');
        }

        $code = $promotion->code;
        $promotion->delete();

        return redirect()->route('admin.promotions.index')->with('success', "Coupon '{$code}' deleted.");
    }
}
