<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\View\View;

class BlockedUserController extends Controller
{
    public function index(): View
    {
        $users = User::query()
            ->where('is_blocked', true)
            ->with('department')
            ->orderBy('name')
            ->paginate(20);

        return view('admin.blocked-users.index', [
            'users' => $users,
        ]);
    }
}
