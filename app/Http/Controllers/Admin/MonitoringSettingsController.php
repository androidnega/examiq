<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\View\View;

class MonitoringSettingsController extends Controller
{
    public function edit(): View
    {
        return view('admin.monitoring.edit', [
            'monitoringBannerEnabled' => Cache::get('examiq.monitoring_banner_enabled', true),
        ]);
    }

    public function update(Request $request): RedirectResponse
    {
        Cache::forever('examiq.monitoring_banner_enabled', $request->boolean('monitoring_banner_enabled'));

        return redirect()
            ->route('dashboard.monitoring.edit')
            ->with('status', __('Monitoring settings saved.'));
    }
}
