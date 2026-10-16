<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\AuditLogFilterRequest;
use App\Http\Resources\Admin\AuditLogResource;
use App\Models\AuditLog;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class AuditLogController extends Controller
{
    /**
     * Display the audit logs and security trail management dashboard.
     */
    public function index(AuditLogFilterRequest $request): View
    {
        /** @var User $user */
        $user = Auth::user();
        $restaurant = $user->restaurant;

        $filters = $request->validated();
        $perPage = (int) ($filters['per_page'] ?? 15);

        $baseQuery = AuditLog::forRestaurant($restaurant->id);

        // 4 KPI Summary Metrics
        $totalLogsCount = (clone $baseQuery)->count();
        $criticalCount = (clone $baseQuery)->where('severity', AuditLog::SEVERITY_CRITICAL)->count();
        $voidsCount = (clone $baseQuery)->whereIn('action', ['ORDER_VOID', 'ITEM_CANCEL', 'PAYMENT_REFUND'])->count();
        $priceChangesCount = (clone $baseQuery)->whereIn('action', ['PRICE_CHANGE', 'PRODUCT_CREATE', 'PRODUCT_DELETE'])->count();

        // Paginated log records
        $logs = (clone $baseQuery)
            ->filter($filters)
            ->latest('id')
            ->paginate($perPage)
            ->withQueryString();

        // Staff members for filter dropdown
        $staffMembers = User::where('restaurant_id', $restaurant->id)
            ->orderBy('name')
            ->get(['id', 'name']);

        return view('admin.audit-logs.index', [
            'restaurant' => $restaurant,
            'user' => $user,
            'logs' => $logs,
            'staffMembers' => $staffMembers,
            'filters' => $filters,
            'kpis' => [
                'total_count' => $totalLogsCount,
                'critical_count' => $criticalCount,
                'voids_count' => $voidsCount,
                'price_changes_count' => $priceChangesCount,
            ],
        ]);
    }

    /**
     * Return JSON resource collection for real-time AJAX refreshes.
     */
    public function apiData(AuditLogFilterRequest $request): AnonymousResourceCollection
    {
        /** @var User $user */
        $user = Auth::user();
        $restaurant = $user->restaurant;
        $filters = $request->validated();
        $perPage = (int) ($filters['per_page'] ?? 15);

        $logs = AuditLog::forRestaurant($restaurant->id)
            ->filter($filters)
            ->latest('id')
            ->paginate($perPage);

        return AuditLogResource::collection($logs);
    }

    /**
     * Display detail for a specific audit log entry via API Resource.
     */
    public function show(AuditLog $auditLog): JsonResponse
    {
        /** @var User $user */
        $user = Auth::user();

        if ($auditLog->restaurant_id !== $user->restaurant_id) {
            abort(403, 'Unauthorized access to audit record.');
        }

        return (new AuditLogResource($auditLog))->response();
    }

    /**
     * Stream export audit records matching active filters to CSV.
     */
    public function exportCsv(AuditLogFilterRequest $request): StreamedResponse
    {
        /** @var User $user */
        $user = Auth::user();
        $restaurant = $user->restaurant;
        $filters = $request->validated();

        $filename = 'audit_trail_export_'.now()->format('Y_m_d_His').'.csv';

        return response()->streamDownload(function () use ($restaurant, $filters) {
            $handle = fopen('php://output', 'w');

            // UTF-8 BOM for spreadsheet compatibility
            fwrite($handle, "\xEF\xBB\xBF");

            // CSV Header Row complying with Currency MMK rule
            fputcsv($handle, [
                'Log ID',
                'Timestamp',
                'Staff Member',
                'Role',
                'Category',
                'Action',
                'Severity',
                'Resource Type',
                'Resource ID',
                'Old Value',
                'New Value',
                'Metadata Details',
                'IP Address',
                'Currency',
            ]);

            AuditLog::forRestaurant($restaurant->id)
                ->filter($filters)
                ->latest('id')
                ->chunk(200, function ($logs) use ($handle) {
                    foreach ($logs as $log) {
                        fputcsv($handle, [
                            $log->id,
                            $log->created_at?->format('Y-m-d H:i:s'),
                            $log->user_name ?? 'System',
                            $log->role_name ?? 'N/A',
                            $log->category ?? 'GENERAL',
                            $log->action,
                            $log->severity ?? 'INFO',
                            $log->resource_type ?? '-',
                            $log->resource_id ?? '-',
                            $log->old_value ?? '-',
                            $log->new_value ?? '-',
                            is_array($log->metadata) ? json_encode($log->metadata, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) : ($log->metadata ?? '-'),
                            $log->ip_address ?? '-',
                            'MMK',
                        ]);
                    }
                });

            fclose($handle);
        }, $filename, [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Cache-Control' => 'no-store, no-cache',
        ]);
    }
}
