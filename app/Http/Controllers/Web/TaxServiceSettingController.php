<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\UpdateTaxServiceSettingRequest;
use App\Http\Resources\Admin\TaxServiceSettingResource;
use App\Models\AuditLog;
use App\Models\User;
use App\Services\AuditLogService;
use App\Services\TaxCalculationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class TaxServiceSettingController extends Controller
{
    public function __construct(
        protected TaxCalculationService $taxCalculationService
    ) {}

    /**
     * Display the Tax & Service Charge configuration interface.
     */
    public function edit(): View
    {
        /** @var User $user */
        $user = Auth::user();
        $restaurant = $user->restaurant;

        // Sample initial calculation for the 80mm previewer (Subtotal 25,000 MMK)
        $sampleCalculation = $this->taxCalculationService->calculate(
            subtotal: 25000,
            channel: 'dine_in',
            paymentMethod: 'cash',
            restaurant: $restaurant
        );

        return view('admin.settings.tax', [
            'restaurant' => $restaurant,
            'user' => $user,
            'sampleCalculation' => $sampleCalculation,
        ]);
    }

    /**
     * Update Tax & Service Charge settings with validation and audit logging.
     */
    public function update(UpdateTaxServiceSettingRequest $request): JsonResponse|RedirectResponse
    {
        /** @var User $user */
        $user = Auth::user();
        $restaurant = $user->restaurant;

        $oldState = [
            'is_tax_enabled' => $restaurant->is_tax_enabled,
            'tax_name' => $restaurant->tax_name,
            'default_tax_percent' => $restaurant->default_tax_percent,
            'tax_calculation_mode' => $restaurant->tax_calculation_mode,
            'tax_id' => $restaurant->tax_id,
            'is_service_charge_enabled' => $restaurant->is_service_charge_enabled,
            'service_charge_name' => $restaurant->service_charge_name,
            'default_service_charge_percent' => $restaurant->default_service_charge_percent,
            'rounding_method' => $restaurant->rounding_method,
            'rounding_direction' => $restaurant->rounding_direction,
        ];

        $validatedData = $request->validated();
        $restaurant->update($validatedData);

        $newState = [
            'is_tax_enabled' => $restaurant->is_tax_enabled,
            'tax_name' => $restaurant->tax_name,
            'default_tax_percent' => $restaurant->default_tax_percent,
            'tax_calculation_mode' => $restaurant->tax_calculation_mode,
            'tax_id' => $restaurant->tax_id,
            'is_service_charge_enabled' => $restaurant->is_service_charge_enabled,
            'service_charge_name' => $restaurant->service_charge_name,
            'default_service_charge_percent' => $restaurant->default_service_charge_percent,
            'rounding_method' => $restaurant->rounding_method,
            'rounding_direction' => $restaurant->rounding_direction,
        ];

        // Audit Trail Entry
        AuditLogService::log(
            action: 'TAX_SETTINGS_UPDATED',
            category: AuditLog::CATEGORY_FINANCIAL,
            severity: AuditLog::SEVERITY_WARNING,
            resource: $restaurant,
            oldValue: $oldState,
            newValue: $newState,
            metadata: [
                'updated_fields' => array_keys($validatedData),
                'currency' => 'MMK',
                'tax_percent' => $restaurant->default_tax_percent,
                'service_charge_percent' => $restaurant->default_service_charge_percent,
                'tax_mode' => $restaurant->tax_calculation_mode,
            ],
            user: $user,
            restaurantId: $restaurant->id,
        );

        $successMsg = 'အခွန်နှင့် ဝန်ဆောင်ခ ဆက်တင်များကို အောင်မြင်စွာ သိမ်းဆည်းပြီးပါပြီ။';

        if ($request->wantsJson()) {
            return (new TaxServiceSettingResource($restaurant->fresh()))
                ->additional(['message' => $successMsg])
                ->response();
        }

        return redirect()->route('admin.settings.tax')->with('success', $successMsg);
    }

    /**
     * API endpoint returning current tax configuration resource.
     */
    public function apiData(): JsonResponse
    {
        /** @var User $user */
        $user = Auth::user();
        $restaurant = $user->restaurant;

        return (new TaxServiceSettingResource($restaurant))->response();
    }

    /**
     * Simulate live dynamic bill calculation based on query or posted values.
     */
    public function simulate(Request $request): JsonResponse
    {
        /** @var User $user */
        $user = Auth::user();
        $restaurant = $user->restaurant;

        $subtotal = (int) $request->input('subtotal', 25000);
        $channel = (string) $request->input('channel', 'dine_in');
        $paymentMethod = (string) $request->input('payment_method', 'cash');

        // Allow live overrides from client for immediate slider/input simulation before saving
        if ($request->has('override_settings')) {
            $overrides = (array) $request->input('override_settings');
            $clone = clone $restaurant;
            foreach ($overrides as $key => $val) {
                if (in_array($key, $clone->getFillable())) {
                    $clone->{$key} = $val;
                }
            }
            $restaurant = $clone;
        }

        $result = $this->taxCalculationService->calculate(
            subtotal: $subtotal,
            channel: $channel,
            paymentMethod: $paymentMethod,
            restaurant: $restaurant
        );

        return response()->json([
            'success' => true,
            'calculation' => $result,
        ]);
    }
}
