<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\View\View;

class SystemSettingsController extends Controller
{
    public function edit(Request $request): View
    {
        return view('admin.system.edit', [
            'supportEmail' => Cache::get('examiq.support_email', ''),
            'monitoringBannerEnabled' => Cache::get('examiq.monitoring_banner_enabled', true),
            'adminDashboardAutoRefreshEnabled' => Cache::get('examiq.admin_dashboard_auto_refresh_enabled', true),
            'adminDashboardAutoRefreshSeconds' => Cache::get('examiq.admin_dashboard_auto_refresh_seconds', 30),
            'allowHodUserManagement' => Cache::get('examiq.allow_hod_user_management', (bool) config('examiq.allow_hod_user_management', false)),
            'allowHodSubmissionSessionManagement' => Cache::get(
                'examiq.allow_hod_submission_session_management',
                (bool) config('examiq.allow_hod_submission_session_management', false)
            ),
            'smsProvider' => Cache::get('examiq.sms_provider', (string) config('examiq.sms_provider', 'log')),
            'otpLogFallbackEnabled' => Cache::get('examiq.otp_log_fallback_enabled', (bool) config('examiq.otp_log_fallback_enabled', true)),
            'arkaselApiKey' => Cache::get('examiq.arkasel_api_key', (string) config('examiq.arkasel_api_key', '')),
            'arkaselSenderId' => Cache::get('examiq.arkasel_sender_id', (string) config('examiq.arkasel_sender_id', 'EXAMIQ')),
            'arkaselBaseUrl' => Cache::get('examiq.arkasel_base_url', (string) config('examiq.arkasel_base_url', 'https://sms.arkesel.com/api/v2')),
            'appName' => config('app.name'),
            'appUrl' => config('app.url'),
            'timezone' => config('app.timezone'),
            'canResetSystemData' => $request->user()?->isSuperAdmin() ?? false,
        ]);
    }

    public function update(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'support_email' => ['nullable', 'string', 'email', 'max:255'],
            'monitoring_banner_enabled' => ['required', 'boolean'],
            'admin_dashboard_auto_refresh_enabled' => ['required', 'boolean'],
            'admin_dashboard_auto_refresh_seconds' => ['required_if:admin_dashboard_auto_refresh_enabled,1', 'nullable', 'integer', 'min:10', 'max:300'],
            'allow_hod_user_management' => ['nullable', 'boolean'],
            'allow_hod_submission_session_management' => ['nullable', 'boolean'],
            'sms_provider' => ['nullable', 'string', 'in:log,arkasel'],
            'otp_log_fallback_enabled' => ['nullable', 'boolean'],
            'arkasel_api_key' => ['nullable', 'string', 'max:255'],
            'arkasel_sender_id' => ['nullable', 'string', 'max:64'],
            'arkasel_base_url' => ['nullable', 'url', 'max:255'],
        ]);

        Cache::forever('examiq.support_email', $data['support_email'] ?? '');
        Cache::forever('examiq.monitoring_banner_enabled', (bool) $data['monitoring_banner_enabled']);
        Cache::forever('examiq.admin_dashboard_auto_refresh_enabled', (bool) $data['admin_dashboard_auto_refresh_enabled']);
        Cache::forever(
            'examiq.admin_dashboard_auto_refresh_seconds',
            (int) ($data['admin_dashboard_auto_refresh_seconds'] ?? 30),
        );

        if ($request->user()?->isSuperAdmin()) {
            Cache::forever('examiq.allow_hod_user_management', (bool) ($data['allow_hod_user_management'] ?? false));
            Cache::forever('examiq.allow_hod_submission_session_management', (bool) ($data['allow_hod_submission_session_management'] ?? false));
            Cache::forever('examiq.sms_provider', (string) ($data['sms_provider'] ?? 'log'));
            Cache::forever('examiq.otp_log_fallback_enabled', (bool) ($data['otp_log_fallback_enabled'] ?? false));
            Cache::forever('examiq.arkasel_api_key', (string) ($data['arkasel_api_key'] ?? ''));
            Cache::forever('examiq.arkasel_sender_id', (string) ($data['arkasel_sender_id'] ?? 'EXAMIQ'));
            Cache::forever('examiq.arkasel_base_url', (string) ($data['arkasel_base_url'] ?? 'https://sms.arkesel.com/api/v2'));
        }

        return redirect()
            ->route('dashboard.system.edit')
            ->with('status', __('System settings saved. Core app values remain in your environment file.'));
    }
}
