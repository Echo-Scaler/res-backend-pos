<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\UpdateRestaurantSettingsRequest;
use App\Http\Resources\Admin\RestaurantSettingsResource;
use App\Models\AuditLog;
use App\Models\User;
use App\Services\AuditLogService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class RestaurantSettingsController extends Controller
{
    /**
     * Display the restaurant configuration management interface.
     */
    public function edit(): View
    {
        /** @var User $user */
        $user = Auth::user();
        $restaurant = $user->restaurant;

        return view('admin.settings.restaurant', [
            'restaurant' => $restaurant,
            'user' => $user,
        ]);
    }

    /**
     * Update restaurant settings, handle logo/cover uploads, and write audit trail.
     */
    public function update(UpdateRestaurantSettingsRequest $request): JsonResponse|RedirectResponse
    {
        /** @var User $user */
        $user = Auth::user();
        $restaurant = $user->restaurant;

        $oldState = [
            'name' => $restaurant->name,
            'tagline' => $restaurant->tagline,
            'phone' => $restaurant->phone,
            'tax_id' => $restaurant->tax_id,
            'default_tax_percent' => $restaurant->default_tax_percent,
            'default_service_charge_percent' => $restaurant->default_service_charge_percent,
            'printer_paper_width' => $restaurant->printer_paper_width,
            'is_qr_order_enabled' => $restaurant->is_qr_order_enabled,
        ];

        $data = $request->validated();

        // Handle Logo Upload or Removal
        if ($request->boolean('remove_logo')) {
            if ($restaurant->logo && Storage::disk('public')->exists($restaurant->logo)) {
                Storage::disk('public')->delete($restaurant->logo);
            }
            $data['logo'] = null;
        } elseif ($request->hasFile('logo')) {
            if ($restaurant->logo && Storage::disk('public')->exists($restaurant->logo)) {
                Storage::disk('public')->delete($restaurant->logo);
            }
            $data['logo'] = $request->file('logo')->store('logos', 'public');
        } else {
            unset($data['logo']);
        }

        // Handle Cover Image Upload or Removal
        if ($request->boolean('remove_cover')) {
            if ($restaurant->cover_image && Storage::disk('public')->exists($restaurant->cover_image)) {
                Storage::disk('public')->delete($restaurant->cover_image);
            }
            $data['cover_image'] = null;
        } elseif ($request->hasFile('cover_image')) {
            if ($restaurant->cover_image && Storage::disk('public')->exists($restaurant->cover_image)) {
                Storage::disk('public')->delete($restaurant->cover_image);
            }
            $data['cover_image'] = $request->file('cover_image')->store('covers', 'public');
        } else {
            unset($data['cover_image']);
        }

        unset($data['remove_logo'], $data['remove_cover']);

        // Persist update
        $restaurant->update($data);

        $newState = [
            'name' => $restaurant->name,
            'tagline' => $restaurant->tagline,
            'phone' => $restaurant->phone,
            'tax_id' => $restaurant->tax_id,
            'default_tax_percent' => $restaurant->default_tax_percent,
            'default_service_charge_percent' => $restaurant->default_service_charge_percent,
            'printer_paper_width' => $restaurant->printer_paper_width,
            'is_qr_order_enabled' => $restaurant->is_qr_order_enabled,
        ];

        // Audit Trail Entry
        AuditLogService::log(
            action: 'SETTINGS_UPDATE',
            category: AuditLog::CATEGORY_ACCESS_SECURITY,
            severity: AuditLog::SEVERITY_WARNING,
            resource: $restaurant,
            oldValue: $oldState,
            newValue: $newState,
            metadata: [
                'updated_fields' => array_keys($data),
                'has_new_logo' => $request->hasFile('logo'),
                'tax_id' => $restaurant->tax_id,
                'currency' => 'MMK',
            ],
            user: $user,
            restaurantId: $restaurant->id,
        );

        $successMsg = 'ဆိုင်ဆက်တင်များနှင့် အမှတ်တံဆိပ် ပြင်ဆင်မှုများကို အောင်မြင်စွာ သိမ်းဆည်းပြီးပါပြီ။';

        if ($request->wantsJson()) {
            return (new RestaurantSettingsResource($restaurant->fresh()))
                ->additional(['message' => $successMsg])
                ->response();
        }

        return redirect()->route('admin.settings.restaurant')->with('success', $successMsg);
    }

    /**
     * API endpoint returning current settings resource.
     */
    public function apiShow(): JsonResponse
    {
        /** @var User $user */
        $user = Auth::user();
        $restaurant = $user->restaurant;

        return (new RestaurantSettingsResource($restaurant))->response();
    }
}
