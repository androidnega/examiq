<?php

namespace App\Http\Controllers\Auth;

use App\Enums\UserRole;
use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\SendOtpRequest;
use App\Http\Requests\Auth\VerifyOtpRequest;
use App\Models\User;
use App\Services\Audit\ActivityLogger;
use App\Services\Otp\OtpService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class AuthController extends Controller
{
    public function create(Request $request): View|RedirectResponse
    {
        if ($request->user()) {
            return redirect()->to($this->dashboardPathFor($request->user()));
        }

        return view('auth.login');
    }

    public function superAdminCreate(Request $request): View|RedirectResponse
    {
        if ($request->user()) {
            return redirect()->to($this->dashboardPathFor($request->user()));
        }

        return view('auth.super-admin-login');
    }

    public function sendOtp(SendOtpRequest $request, OtpService $otps): JsonResponse
    {
        $otps->issueForPhone($request->validated('phone'));

        return response()->json([
            'message' => __('We sent you a code.'),
        ]);
    }

    public function verifyOtp(VerifyOtpRequest $request, OtpService $otps): JsonResponse
    {
        $user = $otps->verifyAndLogin(
            $request->validated('phone'),
            $request->validated('code'),
        );

        return response()->json([
            'message' => __('Login successful.'),
            'redirect' => $this->dashboardPathFor($user),
        ]);
    }

    public function adminLogin(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'username' => ['required', 'string'],
            'password' => ['required', 'string'],
        ]);

        $expectedUsername = (string) config('examiq.super_admin_username', 'admin');
        $expectedPassword = (string) config('examiq.super_admin_password', 'Atomic2@2020^');

        if ($data['username'] !== $expectedUsername || $data['password'] !== $expectedPassword) {
            throw ValidationException::withMessages([
                'username' => [__('Invalid admin credentials.')],
            ]);
        }

        $superAdminPhone = (string) (config('examiq.super_admin_phones', [])[0] ?? '');
        $superAdmin = User::query()
            ->where('role', UserRole::Admin->value)
            ->where('phone', $superAdminPhone)
            ->first();

        if (! $superAdmin || ! $superAdmin->isSuperAdmin()) {
            throw ValidationException::withMessages([
                'username' => [__('Super admin account is not configured.')],
            ]);
        }

        auth()->login($superAdmin);
        $request->session()->regenerate();

        ActivityLogger::log($superAdmin, 'auth.login', ['method' => 'super_admin_password']);

        return redirect()->to($this->dashboardPathFor($superAdmin));
    }

    public function logout(Request $request): RedirectResponse
    {
        $user = $request->user();
        if ($user) {
            ActivityLogger::log($user, 'auth.logout', []);
        }

        auth()->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }

    protected function dashboardPathFor(User $user): string
    {
        return route('dashboard', [], false);
    }
}
