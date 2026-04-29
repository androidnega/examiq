<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use Illuminate\View\View;

class ActivityLogController extends Controller
{
    public function index(): View
    {
        $logs = ActivityLog::query()
            ->with('user')
            ->orderByDesc('created_at')
            ->paginate(30);

        return view('admin.activity-logs.index', [
            'logs' => $logs,
        ]);
    }
}
