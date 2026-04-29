<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\SendOtpRequest;
use App\Http\Requests\Auth\VerifyOtpRequest;
use App\Services\Otp\OtpService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class OtpAuthController extends Controller
{
    public function create(Request $request): View|RedirectResponse
    {
        if ($request->user()) {
            return redirect()->route('dashboard');
        }

        if ($request->boolean('reset')) {
            $request->session()->forget('otp_phone');

            return redirect()->route('login');
        }

        return view('auth.login');
    }

    public function send(SendOtpRequest $request, OtpService $otps): RedirectResponse
    {
        $normalized = $otps->normalizePhone($request->validated('phone'));

        $otps->issueForPhone($request->validated('phone'));

        $request->session()->put('otp_phone', $normalized);

        return redirect()
            ->route('login')
            ->with('status', __('We sent a verification code to your phone.'));
    }

    public function verify(VerifyOtpRequest $request, OtpService $otps): RedirectResponse
    {
        $otps->verifyAndLogin(
            $request->validated('phone'),
            $request->validated('code'),
        );

        $request->session()->forget('otp_phone');

        return redirect()->intended(route('dashboard'));
    }

    public function destroy(Request $request): RedirectResponse
    {
        auth()->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}
