<?php

namespace App\Http\Controllers\Admin;

use App\Enums\UserRole;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\View\View;

class SystemRoleController extends Controller
{
    public function index(): View
    {
        $roles = collect(UserRole::cases())->map(function (UserRole $role): array {
            return [
                'role' => $role,
                'value' => $role->value,
                'users_count' => User::query()->where('role', $role)->count(),
            ];
        });

        return view('admin.roles.index', [
            'roles' => $roles,
        ]);
    }
}
