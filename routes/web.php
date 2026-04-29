<?php

use App\Http\Controllers\Admin\ActivityLogController;
use App\Http\Controllers\Admin\BlockedUserController;
use App\Http\Controllers\Admin\DepartmentController;
use App\Http\Controllers\Admin\FacultyController;
use App\Http\Controllers\Admin\SystemDataResetController;
use App\Http\Controllers\Admin\SystemRoleController;
use App\Http\Controllers\Admin\SystemSettingsController;
use App\Http\Controllers\Admin\UniversityController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\AdminDashboardController;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DashboardSearchController;
use App\Http\Controllers\ExamOfficerRegistryController;
use App\Http\Controllers\HodDashboardController;
use App\Http\Controllers\LecturerDashboardController;
use App\Http\Controllers\ModerationAssignmentController;
use App\Http\Controllers\ModerationFormController;
use App\Http\Controllers\ModerationReviewController;
use App\Http\Controllers\ModeratorDashboardController;
use App\Http\Controllers\SubmissionApprovalController;
use App\Http\Controllers\SubmissionController;
use App\Http\Controllers\SubmissionRevisionController;
use App\Http\Controllers\SubmissionSessionSettingsController;
use Illuminate\Support\Facades\Route;

Route::middleware('guest')->group(function (): void {
    Route::get('/', [AuthController::class, 'create'])->name('login');
    Route::get('/login', [AuthController::class, 'superAdminCreate'])->name('login.super-admin');
    Route::post('/auth/send-otp', [AuthController::class, 'sendOtp'])
        ->middleware('throttle:6,1')
        ->name('auth.send-otp');
    Route::post('/auth/verify-otp', [AuthController::class, 'verifyOtp'])
        ->middleware('throttle:10,1')
        ->name('auth.verify-otp');
    Route::post('/auth/admin-login', [AuthController::class, 'adminLogin'])
        ->middleware('throttle:10,1')
        ->name('auth.admin-login');
});

Route::middleware(['auth', 'not.blocked'])->group(function (): void {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    Route::prefix('dashboard')->group(function (): void {
        Route::get('/', DashboardController::class)->name('dashboard');
        Route::get('/search', [DashboardSearchController::class, 'index'])->name('dashboard.search');
        Route::get('/submissions/{submission}/moderation-forms', [ModerationFormController::class, 'index'])->name('dashboard.submissions.moderation-forms.index');
        Route::get('/submissions/{submission}/moderation-forms/{moderation}/print', [ModerationFormController::class, 'print'])->name('dashboard.submissions.moderation-forms.print');

        Route::middleware('role:lecturer')->group(function (): void {
            Route::get('/submissions', [SubmissionController::class, 'index'])->name('dashboard.submissions.index');
            Route::get('/submissions/create', [SubmissionController::class, 'create'])->name('dashboard.submissions.create');
            Route::post('/submissions', [SubmissionController::class, 'store'])->name('dashboard.submissions.store');
            Route::get('/submissions/{submission}', [SubmissionController::class, 'show'])->name('dashboard.submissions.show');
            Route::get('/submissions/{submission}/update', [SubmissionController::class, 'edit'])->name('dashboard.submissions.update.edit');
            Route::put('/submissions/{submission}/update', [SubmissionController::class, 'update'])->name('dashboard.submissions.update');
            Route::get('/submissions/{submission}/edit', [SubmissionRevisionController::class, 'edit'])->name('dashboard.submissions.edit');
            Route::put('/submissions/{submission}/edit', [SubmissionRevisionController::class, 'update'])->name('dashboard.submissions.revise.update');
        });

        Route::middleware('role:hod')->group(function (): void {
            Route::get('/approvals', [SubmissionApprovalController::class, 'index'])->name('dashboard.approvals.index');
            Route::get('/department', [ModerationAssignmentController::class, 'index'])->name('dashboard.department.index');
            Route::get('/department/users', [UserController::class, 'departmentIndex'])->name('dashboard.department.users.index');
            Route::post('/department/users', [UserController::class, 'departmentStore'])->name('dashboard.department.users.store');
            Route::put('/department/users/{user}', [UserController::class, 'departmentUpdate'])->name('dashboard.department.users.update');
            Route::post('/department/users/{user}/reset-password', [UserController::class, 'departmentResetPassword'])->name('dashboard.department.users.reset-password');
            Route::post('/department/assign-moderators', [ModerationAssignmentController::class, 'assignModerators'])->name('dashboard.department.assign-moderators');
            Route::get('/department/{examSubmission}', [SubmissionApprovalController::class, 'show'])->name('dashboard.department.show');
            Route::post('/department/{examSubmission}/approve', [SubmissionApprovalController::class, 'approve'])->name('dashboard.department.approve');
            Route::post('/department/{examSubmission}/reject', [SubmissionApprovalController::class, 'reject'])->name('dashboard.department.reject');
            Route::get('/department/session-options', [SubmissionSessionSettingsController::class, 'editForHod'])->name('dashboard.department.session-options.edit');
            Route::put('/department/session-options', [SubmissionSessionSettingsController::class, 'updateForHod'])->name('dashboard.department.session-options.update');
        });

        Route::middleware('role:moderator')->group(function (): void {
            Route::get('/reviews', [ModerationReviewController::class, 'index'])->name('dashboard.reviews.index');
            Route::get('/reviews/{examSubmission}', [ModerationReviewController::class, 'show'])->name('dashboard.reviews.show');
            Route::post('/reviews/{examSubmission}', [ModerationReviewController::class, 'store'])->name('dashboard.reviews.store');
            Route::get('/reviews/{examSubmission}/files/{submissionFile}', [ModerationReviewController::class, 'streamSubmissionFile'])->name('dashboard.reviews.files.show');
        });

        Route::middleware('role:exam_officer')->group(function (): void {
            Route::get('/registry', ExamOfficerRegistryController::class)->name('dashboard.registry');
        });

        Route::middleware('role:admin')
            ->name('dashboard.')
            ->group(function (): void {
                Route::get('/users', [UserController::class, 'index'])->name('users.index');
                Route::post('/users', [UserController::class, 'store'])->name('users.store');
                Route::put('/users/{user}', [UserController::class, 'update'])->name('users.update');
                Route::post('/users/{user}/reset-password', [UserController::class, 'resetPassword'])->name('users.reset-password');
                Route::post('/users/{user}/block', [UserController::class, 'block'])->name('users.block');
                Route::post('/users/{user}/unblock', [UserController::class, 'unblock'])->name('users.unblock');

                Route::resource('universities', UniversityController::class)->except(['show']);
                Route::resource('faculties', FacultyController::class)->except(['show']);
                Route::resource('departments', DepartmentController::class)->except(['show']);

                Route::get('/roles', [SystemRoleController::class, 'index'])->name('roles.index');

                Route::get('/activity-logs', [ActivityLogController::class, 'index'])->name('activity-logs.index');
                Route::redirect('/security-logs', '/dashboard/activity-logs', 301)->name('security-logs.index');
                Route::get('/blocked-users', [BlockedUserController::class, 'index'])->name('blocked-users.index');

                Route::redirect('/monitoring', '/dashboard/system', 301);

                Route::get('/system', [SystemSettingsController::class, 'edit'])->name('system.edit');
                Route::put('/system', [SystemSettingsController::class, 'update'])->name('system.update');
                Route::post('/system/test-sms', [SystemSettingsController::class, 'testSms'])->name('system.test-sms');
                Route::post('/system/reset-data', SystemDataResetController::class)->name('system.reset-data');
                Route::get('/system/session-options', [SubmissionSessionSettingsController::class, 'editForAdmin'])->name('system.session-options.edit');
                Route::put('/system/session-options', [SubmissionSessionSettingsController::class, 'updateForAdmin'])->name('system.session-options.update');
            });
    });
});

/*
| Legacy admin URLs → /dashboard/... (301).
*/
Route::middleware(['auth', 'not.blocked', 'role:admin'])->group(function (): void {
    Route::permanentRedirect('/dashboard/admin', '/dashboard');
    Route::permanentRedirect('/dashboard/admin/{path}', '/dashboard/{path}')->where('path', '.+');
});

/*
| Legacy role-prefixed URLs (GET) → unified /dashboard/... (301).
*/
Route::middleware(['auth', 'not.blocked', 'role:admin'])->get('/admin', function () {
    return redirect()->route('dashboard', [], 301);
});

Route::middleware(['auth', 'not.blocked', 'role:lecturer'])->get('/lecturer/{path?}', function (?string $path = null) {
    $tail = $path !== null && $path !== '' ? '/'.ltrim($path, '/') : '';

    return redirect('/dashboard'.$tail, 301);
})->where('path', '.*');

Route::middleware(['auth', 'not.blocked', 'role:hod'])->get('/hod/{path?}', function (?string $path = null) {
    if ($path === null || $path === '') {
        return redirect()->route('dashboard', [], 301);
    }
    if ($path === 'approvals' || str_starts_with($path, 'approvals/')) {
        return redirect('/dashboard/'.$path, 301);
    }
    if ($path === 'submissions' || str_starts_with($path, 'submissions')) {
        $rest = preg_replace('#^submissions/?#', '', $path);
        $suffix = $rest !== '' && $rest !== '0' ? '/'.$rest : '';

        return redirect('/dashboard/department'.$suffix, 301);
    }

    return redirect()->route('dashboard', [], 301);
})->where('path', '.*');

Route::middleware(['auth', 'not.blocked', 'role:moderator'])->get('/moderator/{path?}', function (?string $path = null) {
    $tail = $path !== null && $path !== '' ? '/'.ltrim($path, '/') : '';

    return redirect('/dashboard'.$tail, 301);
})->where('path', '.*');

Route::middleware(['auth', 'not.blocked', 'role:exam_officer'])->get('/exam-officer', function () {
    return redirect()->route('dashboard', [], 301);
});
