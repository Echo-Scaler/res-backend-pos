<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\ConfirmTwoFactorRequest;
use App\Http\Requests\Admin\RevokeSessionRequest;
use App\Http\Requests\Admin\UpdatePasswordRequest;
use App\Http\Requests\Admin\UpdatePinRequest;
use App\Http\Resources\Admin\AccountSecurityResource;
use App\Models\AuditLog;
use App\Models\User;
use App\Services\AuditLogService;
use App\Services\DatabaseBackupService;
use App\Services\TwoFactorAuthService;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class AccountSecurityController extends Controller
{
    /**
     * Display the Account Security & Master Governance Dashboard.
     */
    public function show(Request $request): View|JsonResponse|AccountSecurityResource
    {
        /** @var User $user */
        $user = Auth::user();

        $currentSessionId = $request->session()->getId();

        // Retrieve active sessions from database sessions table
        $rawSessions = DB::table('sessions')
            ->where('user_id', $user->id)
            ->orderByDesc('last_activity')
            ->get();

        $formattedSessions = $rawSessions->map(function ($s) use ($currentSessionId) {
            $ua = (string) ($s->user_agent ?? '');
            $isCurrent = $s->id === $currentSessionId;

            // Resolve friendly device name
            $deviceType = 'Desktop Terminal';
            $icon = 'ti-device-desktop';

            if (preg_match('/(iPad|Tablet|Android(?!.*Mobile))/i', $ua)) {
                $deviceType = 'Waiter Tablet / POS Pad';
                $icon = 'ti-device-tablet';
            } elseif (preg_match('/(iPhone|Android.*Mobile|Mobile)/i', $ua)) {
                $deviceType = 'Mobile Smartphone';
                $icon = 'ti-device-mobile';
            } elseif (preg_match('/(Windows|Linux)/i', $ua)) {
                $deviceType = 'Cashier Touch POS Register';
                $icon = 'ti-device-desktop-analytics';
            } elseif (preg_match('/Macintosh/i', $ua)) {
                $deviceType = 'Manager Workstation (Mac)';
                $icon = 'ti-device-laptop';
            }

            // Resolve browser name
            $browser = 'Web Browser';
            if (str_contains($ua, 'Chrome')) {
                $browser = 'Google Chrome';
            } elseif (str_contains($ua, 'Safari')) {
                $browser = 'Apple Safari';
            } elseif (str_contains($ua, 'Firefox')) {
                $browser = 'Mozilla Firefox';
            } elseif (str_contains($ua, 'Edge')) {
                $browser = 'Microsoft Edge';
            }

            return [
                'id' => $s->id,
                'ip_address' => $s->ip_address ?: '127.0.0.1',
                'device_type' => $deviceType,
                'browser' => $browser,
                'icon' => $icon,
                'is_current' => $isCurrent,
                'last_activity' => Carbon::createFromTimestamp($s->last_activity)->diffForHumans(),
                'last_activity_exact' => Carbon::createFromTimestamp($s->last_activity)->format('Y-m-d H:i:s'),
            ];
        })->values()->all();

        // If no DB sessions found (e.g. running in test without session saving), synthesize current session
        if (empty($formattedSessions)) {
            $formattedSessions[] = [
                'id' => $currentSessionId,
                'ip_address' => $request->ip() ?: '127.0.0.1',
                'device_type' => 'Cashier Touch POS Register',
                'browser' => 'Web Browser',
                'icon' => 'ti-device-desktop',
                'is_current' => true,
                'last_activity' => 'Active now',
                'last_activity_exact' => now()->format('Y-m-d H:i:s'),
            ];
        }

        // Fetch recent security audit events
        $securityAuditLogs = AuditLog::where('restaurant_id', $user->restaurant_id)
            ->where(function ($q) use ($user) {
                $q->where('category', AuditLog::CATEGORY_ACCESS_SECURITY)
                    ->orWhere('user_id', $user->id);
            })
            ->latest('id')
            ->limit(10)
            ->get();

        // 2FA Setup info if currently in staging mode (session temp secret)
        $staging2FaSecret = session('staging_2fa_secret');
        $staging2FaQrSvg = null;
        if ($staging2FaSecret) {
            $otpAuthUrl = TwoFactorAuthService::getOtpAuthUrl($user, $staging2FaSecret);
            $staging2FaQrSvg = TwoFactorAuthService::renderQrSvg($otpAuthUrl);
        }

        if ($request->wantsJson() || $request->ajax()) {
            return (new AccountSecurityResource($user))->additional([
                'sessions' => $formattedSessions,
                'last_backup_info' => [
                    'last_generated_at' => now()->format('Y-m-d H:i:s'),
                    'backup_format' => 'SQL (MySQL 8.0 Compatible)',
                ],
            ]);
        }

        return view('admin.account.security', [
            'user' => $user,
            'sessions' => $formattedSessions,
            'securityAuditLogs' => $securityAuditLogs,
            'staging2FaSecret' => $staging2FaSecret,
            'staging2FaQrSvg' => $staging2FaQrSvg,
            'recoveryCodes' => session('staging_recovery_codes', []),
        ]);
    }

    /**
     * Update the owner's master password.
     */
    public function updatePassword(UpdatePasswordRequest $request): RedirectResponse|JsonResponse
    {
        /** @var User $user */
        $user = Auth::user();

        $user->forceFill([
            'password' => Hash::make($request->password),
            'password_changed_at' => now(),
        ])->save();

        AuditLogService::log(
            action: 'MASTER_PASSWORD_CHANGED',
            category: AuditLog::CATEGORY_ACCESS_SECURITY,
            severity: AuditLog::SEVERITY_WARNING,
            resource: $user,
            metadata: [
                'ip' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'action_note' => 'Account password updated successfully via Account Security module.',
            ]
        );

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'စကားဝှက် အောင်မြင်စွာ ပြောင်းလဲပြီးပါပြီ။ (Password updated successfully.)',
            ]);
        }

        return redirect()->route('admin.account.security')
            ->with('success', 'စကားဝှက် အောင်မြင်စွာ ပြောင်းလဲပြီးပါပြီ။ (Master Password updated successfully.)');
    }

    /**
     * Update the owner's master approval PIN code.
     */
    public function updatePin(UpdatePinRequest $request): RedirectResponse|JsonResponse
    {
        /** @var User $user */
        $user = Auth::user();

        $user->forceFill([
            'pin_code' => Hash::make($request->pin_code),
        ])->save();

        AuditLogService::log(
            action: 'MASTER_PIN_CHANGED',
            category: AuditLog::CATEGORY_ACCESS_SECURITY,
            severity: AuditLog::SEVERITY_CRITICAL,
            resource: $user,
            metadata: [
                'ip' => $request->ip(),
                'pin_length' => strlen($request->pin_code),
                'action_note' => 'Counter Master Void/Discount Approval PIN updated.',
            ]
        );

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'POS Master PIN အောင်မြင်စွာ သတ်မှတ်ပြီးပါပြီ။ (Master PIN updated successfully.)',
            ]);
        }

        return redirect()->route('admin.account.security')
            ->with('success', 'POS Master PIN အောင်မြင်စွာ သတ်မှတ်ပြီးပါပြီ။ (Counter Approval PIN updated successfully.)');
    }

    /**
     * Initiate Two-Factor Authentication setup (Generates Secret & QR Code).
     */
    public function enableTwoFactor(Request $request): RedirectResponse|JsonResponse
    {
        /** @var User $user */
        $user = Auth::user();

        $secret = TwoFactorAuthService::generateSecretKey();
        $recoveryCodes = TwoFactorAuthService::generateRecoveryCodes();

        // Store temporarily in session until confirmed with valid 6-digit OTP
        session([
            'staging_2fa_secret' => $secret,
            'staging_recovery_codes' => $recoveryCodes,
        ]);

        $otpAuthUrl = TwoFactorAuthService::getOtpAuthUrl($user, $secret);
        $qrSvg = TwoFactorAuthService::renderQrSvg($otpAuthUrl);

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'secret' => $secret,
                'otp_auth_url' => $otpAuthUrl,
                'qr_code_svg' => $qrSvg,
                'recovery_codes' => $recoveryCodes,
            ]);
        }

        return redirect()->route('admin.account.security')
            ->with('info', 'Google Authenticator ဖြင့် QR Code Scan ဖတ်ပြီး 6-digit Code ဖြင့် အတည်ပြုပေးပါ။ (Please scan QR code and confirm with OTP code.)');
    }

    /**
     * Confirm and activate Two-Factor Authentication using 6-digit OTP.
     */
    public function confirmTwoFactor(ConfirmTwoFactorRequest $request): RedirectResponse|JsonResponse
    {
        /** @var User $user */
        $user = Auth::user();

        $secret = session('staging_2fa_secret');
        if (! $secret) {
            $secret = $user->two_factor_secret ? decrypt($user->two_factor_secret) : null;
        }

        if (! $secret) {
            if ($request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => '2FA Setup စတင်ထားခြင်း မရှိသေးပါ။ (2FA setup not initiated.)',
                ], 422);
            }

            return back()->with('error', '2FA Setup စတင်ထားခြင်း မရှိသေးပါ။ ကျေးဇူးပြု၍ Enable Two-Factor ကို ပြန်လည်နှိပ်ပါ။');
        }

        // Verify the OTP code
        if (! TwoFactorAuthService::verify($secret, $request->code)) {
            if ($request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'ရိုက်ထည့်ထားသော 6-digit OTP Code မှားယွင်းနေပါသည်။ (Invalid 2FA verification code.)',
                ], 422);
            }

            return back()->with('error', 'ရိုက်ထည့်ထားသော 6-digit OTP Code မှားယွင်းနေပါသည်။ (Invalid verification code.)');
        }

        $recoveryCodes = session('staging_recovery_codes') ?: TwoFactorAuthService::generateRecoveryCodes();

        $user->forceFill([
            'two_factor_secret' => encrypt($secret),
            'two_factor_recovery_codes' => encrypt(json_encode($recoveryCodes)),
            'two_factor_confirmed_at' => now(),
        ])->save();

        session()->forget(['staging_2fa_secret', 'staging_recovery_codes']);

        AuditLogService::log(
            action: 'TWO_FACTOR_ACTIVATED',
            category: AuditLog::CATEGORY_ACCESS_SECURITY,
            severity: AuditLog::SEVERITY_WARNING,
            resource: $user,
            metadata: [
                'ip' => $request->ip(),
                'action_note' => 'Two-Factor Authentication (2FA) confirmed and activated.',
            ]
        );

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Two-Factor Authentication အောင်မြင်စွာ ဖွင့်လှစ်ပြီးပါပြီ။ (Two-Factor authentication activated.)',
                'recovery_codes' => $recoveryCodes,
            ]);
        }

        return redirect()->route('admin.account.security')
            ->with('success', 'Two-Factor Authentication (2FA) အောင်မြင်စွာ ဖွင့်လှစ်ပြီးပါပြီ။ (2FA enabled successfully.)');
    }

    /**
     * Disable Two-Factor Authentication.
     */
    public function disableTwoFactor(Request $request): RedirectResponse|JsonResponse
    {
        /** @var User $user */
        $user = Auth::user();

        $user->forceFill([
            'two_factor_secret' => null,
            'two_factor_recovery_codes' => null,
            'two_factor_confirmed_at' => null,
        ])->save();

        session()->forget(['staging_2fa_secret', 'staging_recovery_codes']);

        AuditLogService::log(
            action: 'TWO_FACTOR_DEACTIVATED',
            category: AuditLog::CATEGORY_ACCESS_SECURITY,
            severity: AuditLog::SEVERITY_WARNING,
            resource: $user,
            metadata: [
                'ip' => $request->ip(),
                'action_note' => 'Two-Factor Authentication (2FA) disabled.',
            ]
        );

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Two-Factor Authentication အား ပိတ်သိမ်းပြီးပါပြီ။ (Two-factor authentication disabled.)',
            ]);
        }

        return redirect()->route('admin.account.security')
            ->with('success', 'Two-Factor Authentication (2FA) အား ပိတ်သိမ်းပြီးပါပြီ။ (2FA disabled successfully.)');
    }

    /**
     * Remotely revoke a specific session on another POS terminal or tablet.
     */
    public function revokeSession(RevokeSessionRequest $request): RedirectResponse|JsonResponse
    {
        /** @var User $user */
        $user = Auth::user();

        $sessionId = $request->session_id;

        // Prevent revoking current active session via this endpoint
        if ($sessionId === $request->session()->getId()) {
            if ($request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'လက်ရှိ အသုံးပြုနေသော Session အား ဤနေရာမှ ပိတ်၍မရပါ။ Log out ကို အသုံးပြုပါ။',
                ], 422);
            }

            return back()->with('error', 'လက်ရှိ အသုံးပြုနေသော Session အား ဤနေရာမှ ပိတ်၍မရပါ။ Log out ကို အသုံးပြုပါ။');
        }

        DB::table('sessions')
            ->where('id', $sessionId)
            ->where('user_id', $user->id)
            ->delete();

        AuditLogService::log(
            action: 'SESSION_REVOKED_REMOTELY',
            category: AuditLog::CATEGORY_ACCESS_SECURITY,
            severity: AuditLog::SEVERITY_INFO,
            resource: $user,
            metadata: [
                'revoked_session_id' => $sessionId,
                'ip' => $request->ip(),
                'action_note' => 'Remote POS terminal/tablet session revoked by owner.',
            ]
        );

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Session အား အောင်မြင်စွာ ပိတ်သိမ်းပြီးပါပြီ။ (Session revoked successfully.)',
            ]);
        }

        return redirect()->route('admin.account.security')
            ->with('success', 'ရွေးချယ်ထားသော POS/Browser Session အား အောင်မြင်စွာ ပိတ်သိမ်းပြီးပါပြီ။ (Session revoked successfully.)');
    }

    /**
     * Terminate all other sessions on all cashier counters and waiter tablets.
     */
    public function logoutOtherDevices(Request $request): RedirectResponse|JsonResponse
    {
        /** @var User $user */
        $user = Auth::user();

        $currentSessionId = $request->session()->getId();

        $deletedCount = DB::table('sessions')
            ->where('user_id', $user->id)
            ->where('id', '!=', $currentSessionId)
            ->delete();

        AuditLogService::log(
            action: 'SESSIONS_PURGED_ALL_OTHER_DEVICES',
            category: AuditLog::CATEGORY_ACCESS_SECURITY,
            severity: AuditLog::SEVERITY_WARNING,
            resource: $user,
            metadata: [
                'current_session_id' => $currentSessionId,
                'revoked_count' => $deletedCount,
                'ip' => $request->ip(),
                'action_note' => 'All other POS registers, tablets, and browser sessions terminated remotely.',
            ]
        );

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'အခြား စက်အားလုံးပေါ်ရှိ Session များ အောင်မြင်စွာ ပိတ်သိမ်းပြီးပါပြီ။',
                'revoked_count' => $deletedCount,
            ]);
        }

        return redirect()->route('admin.account.security')
            ->with('success', "အခြား စက်များပေါ်ရှိ POS/Browser Session ({$deletedCount}) ခု အားလုံးကို အောင်မြင်စွာ ပိတ်သိမ်းပြီးပါပြီ။ (All other terminal sessions revoked.)");
    }

    /**
     * Download an instant SQL database disaster recovery backup file.
     */
    public function downloadBackup(Request $request): Response
    {
        /** @var User $user */
        $user = Auth::user();

        $dump = DatabaseBackupService::createDump($user->restaurant);

        AuditLogService::log(
            action: 'DATABASE_DISASTER_BACKUP_DOWNLOADED',
            category: AuditLog::CATEGORY_ACCESS_SECURITY,
            severity: AuditLog::SEVERITY_CRITICAL,
            resource: $user->restaurant,
            metadata: [
                'filename' => $dump['filename'],
                'size_bytes' => $dump['size_bytes'],
                'tables_count' => $dump['tables_count'],
                'ip' => $request->ip(),
                'action_note' => 'Executive Database SQL Disaster Backup downloaded.',
            ]
        );

        return response($dump['content'], 200, [
            'Content-Type' => 'application/sql',
            'Content-Disposition' => "attachment; filename=\"{$dump['filename']}\"",
            'Cache-Control' => 'no-cache, no-store, must-revalidate',
            'Pragma' => 'no-cache',
            'Expires' => '0',
        ]);
    }
}
