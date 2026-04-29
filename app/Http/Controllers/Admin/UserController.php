<?php

namespace App\Http\Controllers\Admin;

use App\Enums\UserRole;
use App\Http\Controllers\Controller;
use App\Models\Department;
use App\Models\User;
use App\Services\Audit\ActivityLogger;
use App\Services\Sms\Contracts\SmsSender;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class UserController extends Controller
{
    public function index(Request $request): View|StreamedResponse
    {
        $query = $this->filteredUserQuery($request, false);

        if ($request->query('export') === 'csv') {
            return $this->streamCsv($this->filteredUserQuery($request, false));
        }

        $users = $query->paginate(20)->withQueryString();

        return view('admin.users.index', [
            'users' => $users,
            'filters' => [
                'q' => trim((string) $request->query('q', '')),
                'role' => (string) $request->query('role', 'all'),
                'status' => (string) $request->query('status', 'all'),
            ],
            'roleCases' => UserRole::cases(),
            'creatableRoleCases' => $this->creatableRoles(),
            'departments' => Department::query()->orderBy('name')->get(['id', 'name']),
            'indexRoute' => route('dashboard.users.index'),
            'storeRoute' => route('dashboard.users.store'),
            'updateRouteName' => 'dashboard.users.update',
            'resetPasswordRouteName' => 'dashboard.users.reset-password',
            'canManageUsers' => true,
            'canBlockUsers' => true,
            'canResetPasswords' => true,
            'canEditUsers' => true,
            'isSuperAdmin' => $request->user()?->isSuperAdmin() ?? false,
            'isDepartmentScoped' => false,
        ]);
    }

    public function departmentIndex(Request $request): View|StreamedResponse
    {
        abort_unless($this->hodManagementEnabled($request), 403);

        $query = $this->filteredUserQuery($request, true);

        if ($request->query('export') === 'csv') {
            return $this->streamCsv($this->filteredUserQuery($request, true));
        }

        $users = $query->paginate(20)->withQueryString();

        $query->where('role', '!=', UserRole::Admin->value);

        return view('admin.users.index', [
            'users' => $users,
            'filters' => [
                'q' => trim((string) $request->query('q', '')),
                'role' => (string) $request->query('role', 'all'),
                'status' => (string) $request->query('status', 'all'),
            ],
            'roleCases' => UserRole::cases(),
            'creatableRoleCases' => $this->creatableRoles(),
            'departments' => Department::query()->whereKey($request->user()?->department_id)->get(['id', 'name']),
            'indexRoute' => route('dashboard.department.users.index'),
            'storeRoute' => route('dashboard.department.users.store'),
            'updateRouteName' => 'dashboard.department.users.update',
            'resetPasswordRouteName' => 'dashboard.department.users.reset-password',
            'canManageUsers' => true,
            'canBlockUsers' => false,
            'canResetPasswords' => true,
            'canEditUsers' => true,
            'isSuperAdmin' => false,
            'isDepartmentScoped' => true,
        ]);
    }

    /**
     * @return Builder<User>
     */
    protected function filteredUserQuery(Request $request, bool $departmentScoped): Builder
    {
        $query = User::query()->with('department')->orderBy('name');

        if ($departmentScoped) {
            $departmentId = $request->user()?->department_id;
            $query->where(function (Builder $sub) use ($departmentId): void {
                $sub->where('department_id', $departmentId)
                    ->orWhere('role', UserRole::ExamOfficer->value);
            });
        }

        $q = trim((string) $request->query('q', ''));
        if ($q !== '') {
            $term = '%'.str_replace(['%', '_'], ['\\%', '\\_'], $q).'%';
            $query->where(function (Builder $sub) use ($term): void {
                $sub->where('name', 'like', $term)
                    ->orWhere('phone', 'like', $term);
            });
        }

        $roleFilter = $request->query('role');
        if ($roleFilter !== null && $roleFilter !== '' && $roleFilter !== 'all') {
            $roleEnum = UserRole::tryFrom((string) $roleFilter);
            if ($roleEnum !== null) {
                $query->where('role', $roleEnum->value);
            }
        }

        $statusFilter = (string) $request->query('status', 'all');
        if ($statusFilter === 'active') {
            $query->where('is_blocked', false);
        } elseif ($statusFilter === 'blocked') {
            $query->where('is_blocked', true);
        }

        return $query;
    }

    public function store(Request $request): RedirectResponse
    {
        $user = $request->user();
        abort_unless($user !== null, 403);
        abort_unless($user->role === UserRole::Admin, 403);

        $data = $this->validateCreateUser($request);
        $departmentId = $this->resolveDepartmentIdForCreator($user, $data['role'], $data['department_id'] ?? null);

        $created = User::query()->create([
            'name' => $data['name'],
            'phone' => $data['phone'],
            'role' => $data['role'],
            'department_id' => $departmentId,
            'is_blocked' => false,
            'password' => null,
        ]);

        ActivityLogger::log($user, 'user.created', ['target_user_id' => $created->getKey(), 'by' => 'admin']);

        return back()->with('status', __('User account created.'));
    }

    public function departmentStore(Request $request): RedirectResponse
    {
        $user = $request->user();
        abort_unless($user !== null, 403);
        abort_unless($this->hodManagementEnabled($request), 403);

        $data = $this->validateCreateUser($request);
        $departmentId = $this->resolveDepartmentIdForCreator($user, $data['role'], $data['department_id'] ?? null);

        $created = User::query()->create([
            'name' => $data['name'],
            'phone' => $data['phone'],
            'role' => $data['role'],
            'department_id' => $departmentId,
            'is_blocked' => false,
            'password' => null,
        ]);

        ActivityLogger::log($user, 'user.created', ['target_user_id' => $created->getKey(), 'by' => 'hod']);

        return back()->with('status', __('User account created.'));
    }

    public function update(Request $request, User $user): RedirectResponse
    {
        $actor = $request->user();
        abort_unless($actor !== null, 403);
        abort_unless($actor->role === UserRole::Admin, 403);
        abort_unless($this->canAdminEditTarget($actor, $user), 403);

        $data = $this->validateUpdateUser($request, $user, $actor->isSuperAdmin());
        $departmentId = $this->resolveDepartmentIdForCreator($actor, $data['role'], $data['department_id'] ?? null);

        $user->update([
            'name' => $data['name'],
            'phone' => $data['phone'],
            'role' => $data['role'],
            'department_id' => $departmentId,
        ]);

        ActivityLogger::log($actor, 'user.updated', ['target_user_id' => $user->getKey(), 'by' => 'admin']);

        return back()->with('status', __('User account updated.'));
    }

    public function departmentUpdate(Request $request, User $user): RedirectResponse
    {
        $actor = $request->user();
        abort_unless($actor !== null, 403);
        abort_unless($this->hodManagementEnabled($request), 403);
        abort_unless($this->canHodManageTarget($actor, $user), 403);

        $data = $this->validateUpdateUser($request, $user, false);
        $departmentId = $this->resolveDepartmentIdForCreator($actor, $data['role'], $data['department_id'] ?? null);

        $user->update([
            'name' => $data['name'],
            'phone' => $data['phone'],
            'role' => $data['role'],
            'department_id' => $departmentId,
        ]);

        ActivityLogger::log($actor, 'user.updated', ['target_user_id' => $user->getKey(), 'by' => 'hod']);

        return back()->with('status', __('User account updated.'));
    }

    public function resetPassword(Request $request, User $user, SmsSender $smsSender): RedirectResponse
    {
        $actor = $request->user();
        abort_unless($actor !== null, 403);
        abort_unless($actor->role === UserRole::Admin, 403);
        abort_unless($this->canAdminEditTarget($actor, $user), 403);

        return $this->performPasswordReset($actor, $user, $smsSender, 'admin');
    }

    public function departmentResetPassword(Request $request, User $user, SmsSender $smsSender): RedirectResponse
    {
        $actor = $request->user();
        abort_unless($actor !== null, 403);
        abort_unless($this->hodManagementEnabled($request), 403);
        abort_unless($this->canHodManageTarget($actor, $user), 403);

        return $this->performPasswordReset($actor, $user, $smsSender, 'hod');
    }

    /**
     * @param  Builder<User>  $query
     */
    protected function streamCsv(Builder $query): StreamedResponse
    {
        $filename = 'users-'.now()->format('Y-m-d').'.csv';

        return response()->streamDownload(function () use ($query): void {
            $out = fopen('php://output', 'w');
            if ($out === false) {
                return;
            }
            fwrite($out, "\xEF\xBB\xBF");
            fputcsv($out, ['id', 'name', 'phone', 'role', 'department', 'blocked', 'created_at']);
            foreach ($query->clone()->orderBy('name')->cursor() as $user) {
                /** @var User $user */
                fputcsv($out, [
                    (string) $user->getKey(),
                    $user->name,
                    $user->phone,
                    $user->role instanceof UserRole ? $user->role->value : (string) $user->role,
                    $user->department?->name ?? '',
                    $user->is_blocked ? '1' : '0',
                    $user->created_at?->toIso8601String() ?? '',
                ]);
            }
            fclose($out);
        }, $filename, [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
    }

    public function block(User $user): RedirectResponse
    {
        abort_if($user->getKey() === auth()->id(), 403);

        $user->update(['is_blocked' => true]);
        ActivityLogger::log(auth()->user(), 'user.blocked', ['target_user_id' => $user->getKey()]);

        return back()->with('status', __('User blocked.'));
    }

    public function unblock(User $user): RedirectResponse
    {
        $user->update(['is_blocked' => false]);
        ActivityLogger::log(auth()->user(), 'user.unblocked', ['target_user_id' => $user->getKey()]);

        return back()->with('status', __('User unblocked.'));
    }

    private function validateCreateUser(Request $request): array
    {
        return $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'phone' => ['required', 'string', 'max:32', 'unique:users,phone'],
            'role' => ['required', 'string', 'in:lecturer,moderator,exam_officer'],
            'department_id' => ['nullable', 'uuid', 'exists:departments,id'],
        ]);
    }

    private function validateUpdateUser(Request $request, User $targetUser, bool $allowAdminRole): array
    {
        $roles = $allowAdminRole
            ? 'lecturer,moderator,exam_officer,admin'
            : 'lecturer,moderator,exam_officer';

        return $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'phone' => ['required', 'string', 'max:32', 'unique:users,phone,'.$targetUser->getKey()],
            'role' => ['required', 'string', 'in:'.$roles],
            'department_id' => ['nullable', 'uuid', 'exists:departments,id'],
        ]);
    }

    /**
     * @param  array<int, UserRole>  $roles
     */
    private function creatableRoles(): array
    {
        return [
            UserRole::Lecturer,
            UserRole::Moderator,
            UserRole::ExamOfficer,
        ];
    }

    private function resolveDepartmentIdForCreator(User $creator, string $role, ?string $departmentId): ?string
    {
        if ($role === UserRole::ExamOfficer->value || $role === UserRole::Admin->value) {
            return null;
        }

        if ($creator->role === UserRole::Hod) {
            abort_if($creator->department_id === null, 403);

            return (string) $creator->department_id;
        }

        return $departmentId;
    }

    private function hodManagementEnabled(Request $request): bool
    {
        return $request->user()?->role === UserRole::Hod
            && (bool) Cache::get('examiq.allow_hod_user_management', (bool) config('examiq.allow_hod_user_management', false));
    }

    private function canAdminEditTarget(User $admin, User $target): bool
    {
        if ($admin->isSuperAdmin()) {
            return true;
        }

        if ($target->role === UserRole::Admin && $target->getKey() !== $admin->getKey()) {
            return false;
        }

        return true;
    }

    private function canHodManageTarget(User $hod, User $target): bool
    {
        if ($target->role === UserRole::Admin || $target->role === UserRole::Hod) {
            return false;
        }

        if ($target->role === UserRole::ExamOfficer) {
            return true;
        }

        return $hod->department_id !== null && $target->department_id === $hod->department_id;
    }

    private function performPasswordReset(
        User $actor,
        User $targetUser,
        SmsSender $smsSender,
        string $by,
    ): RedirectResponse {
        $temporaryPassword = 'Tmp-'.Str::upper(Str::random(4)).random_int(100, 999).'!';

        $targetUser->update(['password' => $temporaryPassword]);

        $smsSender->send(
            (string) $targetUser->phone,
            __('Your :app temporary password is: :password', [
                'app' => config('app.name'),
                'password' => $temporaryPassword,
            ]),
        );

        ActivityLogger::log($actor, 'user.password_reset', ['target_user_id' => $targetUser->getKey(), 'by' => $by]);

        return back()->with('status', __('Temporary password sent to user via SMS.'));
    }
}
