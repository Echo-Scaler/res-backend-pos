<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Table\StoreDiningTableRequest;
use App\Http\Requests\Admin\Table\TransferDiningTableRequest;
use App\Http\Requests\Admin\Table\UpdateDiningTableRequest;
use App\Http\Requests\Admin\Table\UpdateDiningTableStatusRequest;
use App\Http\Resources\DiningTableResource;
use App\Models\DiningTable;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class DiningTableController extends Controller
{
    /**
     * Display dining floor tables and status overview.
     */
    public function index(Request $request): View|JsonResponse
    {
        $user = $request->user();
        $restaurant = $user->restaurant;

        $areaFilter = $request->query('area');
        $statusFilter = $request->query('status');
        $search = $request->query('search');

        $query = $restaurant->diningTables()
            ->with([
                'currentOrder.items',
                'orders' => function ($q) {
                    $q->whereNotIn('status', ['COMPLETED', 'CANCELLED', 'VOID'])
                        ->with('items')
                        ->latest('id');
                },
            ])
            ->orderByRaw('(table_number + 0) ASC, table_number ASC');

        if ($areaFilter) {
            $query->where('floor_area', $areaFilter);
        }

        if ($statusFilter) {
            $query->where('status', $statusFilter);
        }

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('table_number', 'like', "%{$search}%")
                    ->orWhere('name', 'like', "%{$search}%");
            });
        }

        $tables = $query->get();

        // Calculate floor metrics and get all tables (1 to 47) sorted naturally for modals
        $allTables = $restaurant->diningTables()
            ->orderByRaw('(table_number + 0) ASC, table_number ASC')
            ->get();
        $metrics = [
            'total_tables' => $allTables->count(),
            'vacant_tables' => $allTables->where('status', 'VACANT')->count(),
            'occupied_tables' => $allTables->whereIn('status', ['OCCUPIED', 'ORDERING'])->count(),
            'billing_tables' => $allTables->where('status', 'BILLING')->count(),
            'total_capacity' => $allTables->sum('seating_capacity'),
        ];

        // Unique floor areas for filtering
        $floorAreas = $allTables->pluck('floor_area')->unique()->filter()->values();
        if ($floorAreas->isEmpty()) {
            $floorAreas = collect(['Main Dining Hall', 'Outdoor Terrace', 'VIP Room', 'Bar Area']);
        }

        // Available vacant tables for customer table change / transfer
        $vacantTables = $allTables->where('status', 'VACANT')->where('is_active', true)->values();

        if ($request->wantsJson()) {
            return response()->json([
                'metrics' => $metrics,
                'tables' => DiningTableResource::collection($tables),
                'vacant_tables' => DiningTableResource::collection($vacantTables),
                'floor_areas' => $floorAreas,
            ]);
        }

        return view('admin.tables.index', compact('tables', 'allTables', 'metrics', 'floorAreas', 'areaFilter', 'statusFilter', 'search', 'restaurant', 'vacantTables'));
    }

    /**
     * Store a newly created dining table in storage.
     */
    public function store(StoreDiningTableRequest $request): RedirectResponse|JsonResponse
    {
        $restaurant = $request->user()->restaurant;

        $data = $request->validated();
        $data['restaurant_id'] = $restaurant->id;
        $data['status'] = $data['status'] ?? 'VACANT';
        $data['is_active'] = $request->boolean('is_active', true);

        $table = DiningTable::create($data);

        if ($request->wantsJson()) {
            return response()->json([
                'message' => 'Dining table created successfully with unique QR token.',
                'table' => new DiningTableResource($table),
            ], 201);
        }

        return redirect()->route('admin.tables.index')->with('success', "Table {$table->table_number} created successfully.");
    }

    /**
     * Display table details and QR code.
     */
    public function show(DiningTable $table, Request $request): JsonResponse|View
    {
        $this->authorizeTable($request, $table);

        if ($request->wantsJson()) {
            return response()->json([
                'table' => new DiningTableResource($table),
                'qr_svg' => $table->getQrCodeSvg(),
                'qr_data_uri' => $table->getQrCodeDataUri(),
                'order_url' => $table->getOrderUrl(),
            ]);
        }

        return view('admin.tables.show', compact('table'));
    }

    /**
     * Update the specified dining table.
     */
    public function update(UpdateDiningTableRequest $request, DiningTable $table): RedirectResponse|JsonResponse
    {
        $this->authorizeTable($request, $table);

        $data = $request->validated();
        if ($request->has('is_active')) {
            $data['is_active'] = $request->boolean('is_active');
        }

        $table->update($data);

        if ($request->wantsJson()) {
            return response()->json([
                'message' => "Table {$table->table_number} updated successfully.",
                'table' => new DiningTableResource($table),
            ]);
        }

        return redirect()->route('admin.tables.index')->with('success', "Table {$table->table_number} updated successfully.");
    }

    /**
     * Update table status quickly from cards (e.g. Set VACANT, Set OCCUPIED, Set BILLING).
     */
    public function updateStatus(UpdateDiningTableStatusRequest $request, DiningTable $table): JsonResponse|RedirectResponse
    {
        $this->authorizeTable($request, $table);

        $status = $request->validated('status');
        $table->status = $status;

        // If table reset to VACANT, detach current order
        if ($status === 'VACANT') {
            $table->current_order_id = null;
        }

        $table->save();

        if ($request->wantsJson()) {
            $allTables = $request->user()->restaurant->diningTables()->get();
            $metrics = [
                'total_tables' => $allTables->count(),
                'vacant_tables' => $allTables->where('status', 'VACANT')->count(),
                'occupied_tables' => $allTables->whereIn('status', ['OCCUPIED', 'ORDERING'])->count(),
                'billing_tables' => $allTables->where('status', 'BILLING')->count(),
                'total_capacity' => (int) $allTables->sum('seating_capacity'),
            ];

            return response()->json([
                'message' => "Table {$table->table_number} status set to {$status}.",
                'table' => new DiningTableResource($table),
                'metrics' => $metrics,
            ]);
        }

        return redirect()->back()->with('success', "Table {$table->table_number} status changed to {$status}.");
    }

    /**
     * Regenerate table QR token for security / re-issuance.
     */
    public function regenerateQr(DiningTable $table, Request $request): JsonResponse|RedirectResponse
    {
        $this->authorizeTable($request, $table);

        $table->regenerateQrToken();

        if ($request->wantsJson()) {
            return response()->json([
                'message' => "New QR Code generated for Table {$table->table_number}.",
                'order_url' => $table->getOrderUrl(),
                'qr_data_uri' => $table->getQrCodeDataUri(),
                'qr_token' => $table->qr_token,
            ]);
        }

        return redirect()->back()->with('success', "New QR Code generated for Table {$table->table_number}.");
    }

    /**
     * Print single acrylic table stand (Saizeriya-style Table Tent).
     */
    public function printStand(DiningTable $table, Request $request): View
    {
        $this->authorizeTable($request, $table);
        $restaurant = $request->user()->restaurant;

        return view('admin.tables.print-stand', compact('table', 'restaurant'));
    }

    /**
     * Transfer/move customer seating and active order from source table to target table.
     */
    public function transfer(TransferDiningTableRequest $request, DiningTable $table): JsonResponse|RedirectResponse
    {
        $this->authorizeTable($request, $table);
        $restaurant = $request->user()->restaurant;

        $targetTable = DiningTable::where('restaurant_id', $restaurant->id)
            ->findOrFail($request->validated('target_table_id'));

        $reason = $request->validated('reason');

        DB::transaction(function () use ($table, $targetTable, $reason, $request) {
            // Find active order on source table
            $activeOrder = $table->currentOrder ?? $table->orders()
                ->whereNotIn('status', ['COMPLETED', 'CANCELLED', 'VOID'])
                ->latest('id')
                ->first();

            $sourcePrevStatus = $table->status;

            if ($activeOrder) {
                // Update order's table number to target table
                $activeOrder->table_number = $targetTable->table_number;

                // Add transfer audit trail to order if cancellation_reason field available
                $transferLog = now()->format('Y-m-d H:i')." [Table Moved]: Transferred from Table {$table->table_number} to {$targetTable->table_number} by {$request->user()->name}".($reason ? " (Reason: {$reason})" : '');
                $activeOrder->cancellation_reason = trim(($activeOrder->cancellation_reason ? $activeOrder->cancellation_reason."\n" : '').$transferLog);
                $activeOrder->save();

                // Bind order to target table and copy status
                $targetTable->current_order_id = $activeOrder->id;
                $targetTable->status = $sourcePrevStatus === 'VACANT' ? 'OCCUPIED' : $sourcePrevStatus;
            } else {
                // Transfer status if no order record exists yet
                $targetTable->status = $sourcePrevStatus === 'VACANT' ? 'OCCUPIED' : $sourcePrevStatus;
                $targetTable->current_order_id = null;
            }

            // Append transfer note on target table if needed
            if ($reason) {
                $targetTable->notes = trim(($targetTable->notes ? $targetTable->notes.' | ' : '')."Moved from {$table->table_number}: {$reason}");
            }

            // Free the source table to VACANT
            $table->current_order_id = null;
            $table->status = 'VACANT';

            $targetTable->save();
            $table->save();
        });

        $message = "Successfully transferred from Table {$table->table_number} to Table {$targetTable->table_number}.";

        if ($request->wantsJson()) {
            $allTables = $restaurant->diningTables()->get();
            $metrics = [
                'total_tables' => $allTables->count(),
                'vacant_tables' => $allTables->where('status', 'VACANT')->count(),
                'occupied_tables' => $allTables->whereIn('status', ['OCCUPIED', 'ORDERING'])->count(),
                'billing_tables' => $allTables->where('status', 'BILLING')->count(),
                'total_capacity' => (int) $allTables->sum('seating_capacity'),
            ];

            return response()->json([
                'message' => $message,
                'source_table' => new DiningTableResource($table->fresh()),
                'target_table' => new DiningTableResource($targetTable->fresh()),
                'metrics' => $metrics,
            ]);
        }

        return redirect()->route('admin.tables.index')->with('success', $message);
    }

    /**
     * Batch print all table stands.
     */
    public function batchPrintStands(Request $request): View
    {
        $restaurant = $request->user()->restaurant;
        $areaFilter = $request->query('area');

        $query = $restaurant->diningTables()->active()->orderByRaw('(table_number + 0) ASC, table_number ASC');
        if ($areaFilter) {
            $query->where('floor_area', $areaFilter);
        }

        $tables = $query->get();

        return view('admin.tables.batch-print', compact('tables', 'restaurant', 'areaFilter'));
    }

    /**
     * Remove the specified table from storage.
     */
    public function destroy(DiningTable $table, Request $request): RedirectResponse|JsonResponse
    {
        $this->authorizeTable($request, $table);

        if (in_array($table->status, ['OCCUPIED', 'BILLING'])) {
            $errorMsg = "Cannot delete Table {$table->table_number} while customers are seated ({$table->status}). Please settle or reset the table first.";
            if ($request->wantsJson()) {
                return response()->json(['error' => $errorMsg], 422);
            }

            return redirect()->back()->with('error', $errorMsg);
        }

        $tableNumber = $table->table_number;
        $table->delete();

        if ($request->wantsJson()) {
            return response()->json(['message' => "Table {$tableNumber} deleted successfully."]);
        }

        return redirect()->route('admin.tables.index')->with('success', "Table {$tableNumber} deleted successfully.");
    }

    /**
     * Authorize that the table belongs to the user's restaurant.
     */
    private function authorizeTable(Request $request, DiningTable $table): void
    {
        if ($table->restaurant_id !== $request->user()->restaurant_id) {
            abort(403, 'Unauthorized access to this restaurant table.');
        }
    }
}
