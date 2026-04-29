<?php

return [

    'otp_ttl_minutes' => (int) env('EXAMIQ_OTP_TTL', 5),

    'login_hero_url' => env(
        'EXAMIQ_LOGIN_HERO_URL',
        'https://static.vecteezy.com/system/resources/thumbnails/071/784/544/small/student-s-hand-writing-on-paper-with-pen-in-classroom-setting-exam-time-free-photo.jpg',
    ),

    /** Public path (relative to /public) for the default profile avatar image. */
    'default_avatar' => env('EXAMIQ_DEFAULT_AVATAR', 'images/avatars/default-profile.jpg'),

    /*
    |--------------------------------------------------------------------------
    | Super admin phones
    |--------------------------------------------------------------------------
    |
    | Comma-separated list in env (EXAMIQ_SUPER_ADMIN_PHONES). Only these
    | admin users can perform irreversible platform-level actions like
    | clearing all system data.
    |
    */
    'super_admin_phones' => array_values(array_filter(array_map(
        static fn (string $phone): string => trim($phone),
        explode(',', (string) env('EXAMIQ_SUPER_ADMIN_PHONES', '0200000000'))
    ))),

    'allow_hod_user_management' => (bool) env('EXAMIQ_ALLOW_HOD_USER_MANAGEMENT', false),
    'allow_hod_submission_session_management' => (bool) env('EXAMIQ_ALLOW_HOD_SUBMISSION_SESSION_MANAGEMENT', false),
    'submission_academic_year_options' => ['2025/2026'],
    'submission_semester_options' => ['first', 'second'],
    'sms_provider' => (string) env('EXAMIQ_SMS_PROVIDER', 'log'),
    'arkasel_api_key' => (string) env('ARKASEL_API_KEY', ''),
    'arkasel_sender_id' => (string) env('ARKASEL_SENDER_ID', 'EXAMIQ'),
    'arkasel_base_url' => (string) env('ARKASEL_BASE_URL', 'https://sms.arkesel.com/api/v2'),
    'otp_log_fallback_enabled' => (bool) env('EXAMIQ_OTP_LOG_FALLBACK_ENABLED', true),
    'super_admin_username' => (string) env('EXAMIQ_SUPER_ADMIN_USERNAME', 'admin'),
    'super_admin_password' => (string) env('EXAMIQ_SUPER_ADMIN_PASSWORD', 'Atomic2@2020^'),
];
