<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\ResetSystemDataRequest;
use App\Services\System\SystemDataResetService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;

class SystemDataResetController extends Controller
{
    public function __invoke(
        ResetSystemDataRequest $request,
        SystemDataResetService $systemDataResetService,
    ): RedirectResponse {
        $superAdmin = $request->user();
        abort_unless($superAdmin !== null, 403);

        $resetLevel = (string) $request->validated('reset_level');

        $systemDataResetService->resetByLevel($superAdmin, $resetLevel);

        $refreshedSuperAdmin = \App\Models\User::query()->where('phone', $superAdmin->phone)->first();
        abort_unless($refreshedSuperAdmin !== null, 403);

        Auth::login($refreshedSuperAdmin);
        $request->session()->regenerate();
        $request->session()->regenerateToken();

        return redirect()
            ->route('dashboard.system.edit')
            ->with('status', __('Data reset completed successfully.'));
    }
}
