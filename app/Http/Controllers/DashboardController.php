<?php

namespace App\Http\Controllers;

use App\Enums\UserRole;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __invoke(Request $request): View|RedirectResponse
    {
        $user = $request->user();
        $role = $user->role instanceof UserRole ? $user->role : UserRole::tryFrom((string) $user->role);

        if ($role === UserRole::ExamOfficer) {
            return app(ExamOfficerHomeController::class)($request);
        }

        if ($role === null) {
            abort(403);
        }

        return match ($role) {
            UserRole::Admin => app(AdminDashboardController::class)($request),
            UserRole::Hod => app(HodDashboardController::class)($request),
            UserRole::Lecturer => app(LecturerDashboardController::class)($request),
            UserRole::Moderator => app(ModeratorDashboardController::class)($request),
            default => abort(403),
        };
    }
}
