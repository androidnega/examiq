@extends('layouts.app', ['header' => __('System Settings')])

@section('content')
    <p class="mb-6 text-sm text-slate-500">{{ __('Read-only core values come from environment settings, while switches below control runtime behavior.') }}</p>

    <div class="space-y-6">
        <dl class="grid gap-4 rounded-xl border border-slate-200/80 bg-white p-6 text-sm sm:grid-cols-3">
            <div>
                <dt class="font-medium text-slate-500">{{ __('Application name') }}</dt>
                <dd class="mt-1 text-slate-900">{{ $appName }}</dd>
            </div>
            <div>
                <dt class="font-medium text-slate-500">{{ __('Application URL') }}</dt>
                <dd class="mt-1 break-all font-mono text-xs text-slate-800">{{ $appUrl }}</dd>
            </div>
            <div>
                <dt class="font-medium text-slate-500">{{ __('Timezone') }}</dt>
                <dd class="mt-1 font-mono text-xs text-slate-800">{{ $timezone }}</dd>
            </div>
        </dl>

        <form method="post" action="{{ route('dashboard.system.update') }}" class="space-y-6 rounded-xl border border-slate-200/80 bg-white p-6">
            @csrf
            @method('PUT')

            <div class="grid gap-6 lg:grid-cols-2">
                <div class="space-y-1 rounded-lg border border-slate-100 bg-slate-50/60 p-4">
                    <div class="flex items-start justify-between gap-3">
                        <div>
                            <p class="text-sm font-medium text-slate-900">{{ __('Monitoring banner') }}</p>
                            <p class="text-xs text-slate-500">{{ __('Show security/compliance notice to signed-in users.') }}</p>
                        </div>
                        <label class="relative inline-flex cursor-pointer items-center">
                            <input type="hidden" name="monitoring_banner_enabled" value="0" />
                            <input
                                type="checkbox"
                                name="monitoring_banner_enabled"
                                value="1"
                                class="peer sr-only"
                                @checked(old('monitoring_banner_enabled', $monitoringBannerEnabled))
                            />
                            <span class="h-6 w-11 rounded-full bg-slate-300 transition peer-checked:bg-teal-500"></span>
                            <span class="absolute left-0.5 top-0.5 h-5 w-5 rounded-full bg-white transition peer-checked:translate-x-5"></span>
                        </label>
                    </div>
                    @error('monitoring_banner_enabled')
                        <p class="text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="space-y-1 rounded-lg border border-slate-100 bg-slate-50/60 p-4">
                    <div class="flex items-start justify-between gap-3">
                        <div>
                            <p class="text-sm font-medium text-slate-900">{{ __('Admin dashboard auto-refresh') }}</p>
                            <p class="text-xs text-slate-500">{{ __('Reload analytics automatically to keep trends current.') }}</p>
                        </div>
                        <label class="relative inline-flex cursor-pointer items-center">
                            <input type="hidden" name="admin_dashboard_auto_refresh_enabled" value="0" />
                            <input
                                id="admin_dashboard_auto_refresh_enabled"
                                type="checkbox"
                                name="admin_dashboard_auto_refresh_enabled"
                                value="1"
                                class="peer sr-only"
                                @checked(old('admin_dashboard_auto_refresh_enabled', $adminDashboardAutoRefreshEnabled))
                            />
                            <span class="h-6 w-11 rounded-full bg-slate-300 transition peer-checked:bg-teal-500"></span>
                            <span class="absolute left-0.5 top-0.5 h-5 w-5 rounded-full bg-white transition peer-checked:translate-x-5"></span>
                        </label>
                    </div>
                    @error('admin_dashboard_auto_refresh_enabled')
                        <p class="text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="max-w-sm">
                <label for="admin_dashboard_auto_refresh_seconds" class="block text-sm font-medium text-slate-700">
                    {{ __('Refresh interval (seconds)') }}
                </label>
                <input
                    id="admin_dashboard_auto_refresh_seconds"
                    type="number"
                    name="admin_dashboard_auto_refresh_seconds"
                    min="10"
                    max="300"
                    value="{{ old('admin_dashboard_auto_refresh_seconds', $adminDashboardAutoRefreshSeconds) }}"
                    class="mt-1 w-full rounded-lg border border-slate-200 px-3 py-2 text-sm focus:border-teal-500 focus:outline-none focus:ring-2 focus:ring-teal-500/20"
                />
                @error('admin_dashboard_auto_refresh_seconds')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
                <p class="mt-1 text-xs text-slate-500">{{ __('Used only when auto-refresh is enabled. Allowed range: 10 to 300 seconds.') }}</p>
            </div>

            <div>
                <label for="support_email" class="block text-sm font-medium text-slate-700">{{ __('Support email (optional)') }}</label>
                <input
                    id="support_email"
                    type="email"
                    name="support_email"
                    value="{{ old('support_email', $supportEmail) }}"
                    class="mt-1 w-full rounded-lg border border-slate-200 px-3 py-2 text-sm focus:border-teal-500 focus:outline-none focus:ring-2 focus:ring-teal-500/20"
                    placeholder="support@example.edu"
                />
                @error('support_email')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
                <p class="mt-1 text-xs text-slate-500">{{ __('Shown in internal tools when you add them; not exposed publicly yet.') }}</p>
            </div>

            @if (auth()->user()?->isSuperAdmin())
                <div class="rounded-xl border border-slate-200 bg-slate-50 p-4">
                    <h2 class="text-sm font-semibold text-slate-900">{{ __('Super admin controls') }}</h2>
                    <div class="mt-4 space-y-5">
                        <div class="flex items-start justify-between gap-3">
                            <div>
                                <p class="text-sm font-medium text-slate-900">{{ __('Allow HOD to create users') }}</p>
                                <p class="text-xs text-slate-500">{{ __('If enabled, HOD can create lecturer, moderator, and exam officer accounts.') }}</p>
                            </div>
                            <label class="relative inline-flex cursor-pointer items-center">
                                <input type="hidden" name="allow_hod_user_management" value="0" />
                                <input
                                    type="checkbox"
                                    name="allow_hod_user_management"
                                    value="1"
                                    class="peer sr-only"
                                    @checked(old('allow_hod_user_management', $allowHodUserManagement))
                                />
                                <span class="h-6 w-11 rounded-full bg-slate-300 transition peer-checked:bg-teal-500"></span>
                                <span class="absolute left-0.5 top-0.5 h-5 w-5 rounded-full bg-white transition peer-checked:translate-x-5"></span>
                            </label>
                        </div>

                        <div class="flex items-start justify-between gap-3">
                            <div>
                                <p class="text-sm font-medium text-slate-900">{{ __('Allow HOD to manage academic year and semester options') }}</p>
                                <p class="text-xs text-slate-500">{{ __('If enabled, HOD can update centralized session options used in submission forms.') }}</p>
                            </div>
                            <label class="relative inline-flex cursor-pointer items-center">
                                <input type="hidden" name="allow_hod_submission_session_management" value="0" />
                                <input
                                    type="checkbox"
                                    name="allow_hod_submission_session_management"
                                    value="1"
                                    class="peer sr-only"
                                    @checked(old('allow_hod_submission_session_management', $allowHodSubmissionSessionManagement))
                                />
                                <span class="h-6 w-11 rounded-full bg-slate-300 transition peer-checked:bg-teal-500"></span>
                                <span class="absolute left-0.5 top-0.5 h-5 w-5 rounded-full bg-white transition peer-checked:translate-x-5"></span>
                            </label>
                        </div>

                        <div class="rounded-lg border border-slate-200 bg-white px-3 py-2">
                            <p class="text-sm font-medium text-slate-900">{{ __('Submission session options') }}</p>
                            <p class="text-xs text-slate-500">{{ __('Define academic year and semester choices from one place.') }}</p>
                            <div class="mt-2">
                                <x-button href="{{ route('dashboard.system.session-options.edit') }}" variant="secondary">{{ __('Manage options') }}</x-button>
                            </div>
                        </div>

                        <div class="grid gap-4 md:grid-cols-2">
                            <div>
                                <label for="sms_provider" class="block text-sm font-medium text-slate-700">{{ __('OTP SMS provider') }}</label>
                                <select id="sms_provider" name="sms_provider" class="mt-1 w-full rounded-lg border border-slate-200 px-3 py-2 text-sm">
                                    <option value="log" @selected(old('sms_provider', $smsProvider) === 'log')>{{ __('Laravel log (development)') }}</option>
                                    <option value="arkasel" @selected(old('sms_provider', $smsProvider) === 'arkasel')>{{ __('Arkassel') }}</option>
                                </select>
                            </div>
                            <div class="flex items-center justify-between rounded-lg border border-slate-200 bg-white px-3 py-2">
                                <div>
                                    <p class="text-sm font-medium text-slate-900">{{ __('Enable OTP log fallback') }}</p>
                                    <p class="text-xs text-slate-500">{{ __('If Arkassel fails, write OTP to laravel.log.') }}</p>
                                </div>
                                <label class="relative inline-flex cursor-pointer items-center">
                                    <input type="hidden" name="otp_log_fallback_enabled" value="0" />
                                    <input
                                        type="checkbox"
                                        name="otp_log_fallback_enabled"
                                        value="1"
                                        class="peer sr-only"
                                        @checked(old('otp_log_fallback_enabled', $otpLogFallbackEnabled))
                                    />
                                    <span class="h-6 w-11 rounded-full bg-slate-300 transition peer-checked:bg-teal-500"></span>
                                    <span class="absolute left-0.5 top-0.5 h-5 w-5 rounded-full bg-white transition peer-checked:translate-x-5"></span>
                                </label>
                            </div>
                        </div>

                        <div class="grid gap-4 md:grid-cols-3">
                            <div>
                                <label for="arkasel_api_key" class="block text-sm font-medium text-slate-700">{{ __('Arkassel API key') }}</label>
                                <input id="arkasel_api_key" name="arkasel_api_key" type="text" value="{{ old('arkasel_api_key', $arkaselApiKey) }}" class="mt-1 w-full rounded-lg border border-slate-200 px-3 py-2 text-sm" />
                            </div>
                            <div>
                                <label for="arkasel_sender_id" class="block text-sm font-medium text-slate-700">{{ __('Arkassel sender ID') }}</label>
                                <input id="arkasel_sender_id" name="arkasel_sender_id" type="text" value="{{ old('arkasel_sender_id', $arkaselSenderId) }}" class="mt-1 w-full rounded-lg border border-slate-200 px-3 py-2 text-sm" />
                            </div>
                            <div>
                                <label for="arkasel_base_url" class="block text-sm font-medium text-slate-700">{{ __('Arkassel base URL') }}</label>
                                <input id="arkasel_base_url" name="arkasel_base_url" type="url" value="{{ old('arkasel_base_url', $arkaselBaseUrl) }}" class="mt-1 w-full rounded-lg border border-slate-200 px-3 py-2 text-sm" />
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            <x-button type="submit" variant="primary">{{ __('Save settings') }}</x-button>
        </form>

        <div class="rounded-xl border border-red-200 bg-red-50 p-6">
            <h2 class="text-sm font-semibold text-red-900">{{ __('Danger zone: Reset all system data') }}</h2>
            <p class="mt-1 text-xs text-red-700">
                {{ __('This permanently removes all records (including seeded/dummy data), rebuilds the schema, and keeps only the current super admin account.') }}
            </p>

            @if ($canResetSystemData)
                <form method="post" action="{{ route('dashboard.system.reset-data', [], false) }}" class="mt-4 space-y-3">
                    @csrf
                    <div>
                        <p class="block text-sm font-medium text-red-900">{{ __('Reset level') }}</p>
                        <div class="mt-2 space-y-2 rounded-lg border border-red-200 bg-white p-3">
                            <label class="flex items-start gap-2">
                                <input type="radio" name="reset_level" value="default" class="mt-1" @checked(old('reset_level', 'default') === 'default') />
                                <span>
                                    <span class="block text-sm font-semibold text-red-900">{{ __('Default reset (recommended)') }}</span>
                                    <span class="block text-xs text-red-700">{{ __('Keeps users and master records. Clears only workflow/runtime data.') }}</span>
                                </span>
                            </label>
                            <label class="flex items-start gap-2">
                                <input type="radio" name="reset_level" value="entire_system" class="mt-1" @checked(old('reset_level') === 'entire_system') />
                                <span>
                                    <span class="block text-sm font-semibold text-red-900">{{ __('Entire system reset') }}</span>
                                    <span class="block text-xs text-red-700">{{ __('Rebuilds schema and reseeds defaults, but preserves all user accounts.') }}</span>
                                </span>
                            </label>
                        </div>
                        @error('reset_level')
                            <p class="mt-1 text-sm text-red-700">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="rounded-lg border border-red-200 bg-white p-3">
                        <p class="text-sm font-medium text-red-900">{{ __('Items that will be lost') }}</p>
                        <ul class="mt-2 list-disc space-y-1 pl-5 text-xs text-red-700">
                            <li>{{ __('Exam submissions and attached files') }}</li>
                            <li>{{ __('Moderation assignments and moderation feedback records') }}</li>
                            <li>{{ __('Revision history records') }}</li>
                            <li>{{ __('Activity/security logs') }}</li>
                            <li>{{ __('Outstanding OTP codes') }}</li>
                            <li>{{ __('For entire system reset only: custom universities/faculties/departments/courses and runtime settings are reset to seeded defaults') }}</li>
                        </ul>
                    </div>

                    <div>
                        <label for="confirmation_text" class="block text-sm font-medium text-red-900">{{ __('Type RESET ALL DATA to continue') }}</label>
                        <input
                            id="confirmation_text"
                            type="text"
                            name="confirmation_text"
                            required
                            class="mt-1 w-full rounded-lg border border-red-300 bg-white px-3 py-2 text-sm focus:border-red-500 focus:outline-none focus:ring-2 focus:ring-red-500/20"
                            placeholder="RESET ALL DATA"
                        />
                        @error('confirmation_text')
                            <p class="mt-1 text-sm text-red-700">{{ $message }}</p>
                        @enderror
                    </div>
                    <x-button type="submit" variant="secondary" class="border-red-300 bg-white text-red-800 hover:bg-red-100">
                        {{ __('Reset entire system data') }}
                    </x-button>
                </form>
            @else
                <p class="mt-3 text-sm text-red-700">{{ __('Only a configured super admin can perform this action.') }}</p>
            @endif
        </div>
    </div>
@endsection
