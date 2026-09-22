<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Inventory\StoreInventoryItemRequest;
use App\Http\Requests\Admin\Inventory\UpdateInventoryItemRequest;
use App\Http\Resources\InventoryItemResource;
use App\Models\InventoryItem;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class InventoryController extends Controller
{
    /**
     * Display inventory stocks and alert thresholds workspace.
     */
    public function index(Request $request): View
    {
        $currentUser = $request->user();
        $restaurant = $currentUser->restaurant;

        $query = InventoryItem::query()
            ->where('restaurant_id', $currentUser->restaurant_id);

        if ($request->boolean('low_stock_only')) {
            $query->whereRaw('current_stock <= min_stock_alert');
        }

        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('sku', 'like', "%{$search}%")
                    ->orWhere('supplier_name', 'like', "%{$search}%");
            });
        }

        $items = $query->orderBy('name')->paginate(15)->withQueryString();

        $allItems = InventoryItem::where('restaurant_id', $currentUser->restaurant_id)->get();
        $lowStockCount = $allItems->filter(fn (InventoryItem $i) => $i->isLowStock())->count();
        $totalValuation = $allItems->sum(fn (InventoryItem $i) => $i->current_stock * $i->unit_cost);

        $stats = [
            'total_items' => $allItems->count(),
            'low_stock_count' => $lowStockCount,
            'total_valuation' => $totalValuation,
        ];

        return view('admin.inventory.index', [
            'restaurant' => $restaurant,
            'currentUser' => $currentUser,
            'items' => $items,
            'stats' => $stats,
            'search' => $search,
            'lowStockOnly' => $request->boolean('low_stock_only'),
        ]);
    }

    /**
     * Display or export inventory remaining stock list report.
     */
    public function report(Request $request): View|StreamedResponse
    {
        $currentUser = $request->user();
        $restaurant = $currentUser->restaurant;

        $query = InventoryItem::query()
            ->where('restaurant_id', $currentUser->restaurant_id);

        if ($request->boolean('low_stock_only')) {
            $query->whereRaw('current_stock <= min_stock_alert');
        }

        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('sku', 'like', "%{$search}%")
                    ->orWhere('supplier_name', 'like', "%{$search}%");
            });
        }

        if ($request->input('export') === 'csv') {
            $exportItems = $query->orderBy('name')->get();
            $filename = 'inventory-remaining-stock-'.now()->format('Y-m-d').'.csv';

            return response()->streamDownload(function () use ($exportItems) {
                $handle = fopen('php://output', 'w');
                fputcsv($handle, ['Item Name', 'SKU', 'Unit', 'Remaining Stock', 'Alert Threshold', 'Unit Cost (MMK)', 'Total Valuation (MMK)', 'Status']);
                foreach ($exportItems as $item) {
                    fputcsv($handle, [
                        $item->name,
                        $item->sku ?? 'N/A',
                        $item->unit,
                        $item->current_stock,
                        $item->min_stock_alert,
                        $item->unit_cost,
                        $item->current_stock * $item->unit_cost,
                        $item->isLowStock() ? 'Low Stock Warning' : 'Adequate',
                    ]);
                }
                fclose($handle);
            }, $filename, ['Content-Type' => 'text/csv']);
        }

        $items = $query->orderBy('name')->get();

        $stats = [
            'total_items' => $items->count(),
            'low_stock_count' => $items->filter(fn (InventoryItem $i) => $i->isLowStock())->count(),
            'total_valuation' => $items->sum(fn (InventoryItem $i) => $i->current_stock * $i->unit_cost),
            'total_units' => $items->sum(fn (InventoryItem $i) => $i->current_stock),
        ];

        return view('admin.inventory.report', [
            'restaurant' => $restaurant,
            'currentUser' => $currentUser,
            'items' => $items,
            'stats' => $stats,
            'search' => $search,
            'lowStockOnly' => $request->boolean('low_stock_only'),
        ]);
    }

    /**
     * Store a newly created inventory item.
     */
    public function store(StoreInventoryItemRequest $request): JsonResponse|RedirectResponse
    {
        $currentUser = $request->user();
        $validated = $request->validated();

        $item = InventoryItem::create([
            'restaurant_id' => $currentUser->restaurant_id,
            'name' => $validated['name'],
            'sku' => $validated['sku'] ?? null,
            'unit' => $validated['unit'],
            'current_stock' => $validated['current_stock'],
            'min_stock_alert' => $validated['min_stock_alert'],
            'unit_cost' => $validated['unit_cost'] ?? 0,
            'supplier_name' => $validated['supplier_name'] ?? null,
            'is_active' => $validated['is_active'] ?? true,
        ]);

        $message = "Inventory stock '{$item->name}' added successfully.";

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => $message,
                'item' => new InventoryItemResource($item),
            ]);
        }

        return redirect()->route('admin.inventory.index')->with('success', $message);
    }

    /**
     * Update an inventory item.
     */
    public function update(UpdateInventoryItemRequest $request, InventoryItem $inventoryItem): JsonResponse|RedirectResponse
    {
        $currentUser = $request->user();

        if ($inventoryItem->restaurant_id !== $currentUser->restaurant_id) {
            abort(403, 'Unauthorized access.');
        }

        $validated = $request->validated();
        $inventoryItem->update($validated);

        $message = "Inventory item '{$inventoryItem->name}' updated successfully.";

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => $message,
                'item' => new InventoryItemResource($inventoryItem),
            ]);
        }

        return redirect()->route('admin.inventory.index')->with('success', $message);
    }

    /**
     * Delete an inventory item.
     */
    public function destroy(Request $request, InventoryItem $inventoryItem): RedirectResponse
    {
        $currentUser = $request->user();

        if ($inventoryItem->restaurant_id !== $currentUser->restaurant_id) {
            abort(403, 'Unauthorized access.');
        }

        $itemName = $inventoryItem->name;
        $inventoryItem->delete();

        return redirect()->route('admin.inventory.index')->with('success', "Inventory item '{$itemName}' deleted.");
    }
}
