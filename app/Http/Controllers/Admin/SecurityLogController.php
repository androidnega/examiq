<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use Illuminate\View\View;

class SecurityLogController extends Controller
{
    public function index(): View
    {
        $logs = ActivityLog::query()
            ->with('user')
            ->where(function ($q): void {
                $q->where('action', 'like', 'auth.%')
                    ->orWhere('action', 'like', 'security.%')
                    ->orWhereIn('action', ['user.blocked', 'user.unblocked']);
            })
            ->orderByDesc('created_at')
            ->paginate(30);

        return view('admin.security-logs.index', [
            'logs' => $logs,
        ]);
    }
}
