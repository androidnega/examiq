<?php

namespace App\Services\System;

use App\Models\ActivityLog;
use App\Enums\UserRole;
use App\Models\ExamSubmission;
use App\Models\Moderation;
use App\Models\ModerationAssignment;
use App\Models\Otp;
use App\Models\Revision;
use App\Models\SubmissionFile;
use App\Models\Department;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;

class SystemDataResetService
{
    public const LEVEL_DEFAULT = 'default';
    public const LEVEL_ENTIRE_SYSTEM = 'entire_system';

    public function resetByLevel(User $superAdmin, string $level): void
    {
        if ($level === self::LEVEL_ENTIRE_SYSTEM) {
            $this->resetEntireSystemPreservingUsers();
            return;
        }

        $this->resetOperationalDataOnly();
    }

    private function resetOperationalDataOnly(): void
    {
        // Keep users and master data, but clear workflow/runtime records.
        SubmissionFile::query()->delete();
        Moderation::query()->delete();
        Revision::query()->delete();
        ModerationAssignment::query()->delete();
        ExamSubmission::query()->delete();
        ActivityLog::query()->delete();
        Otp::query()->delete();

        Storage::disk(SubmissionFile::STORAGE_DISK)->deleteDirectory('submissions');
    }

    private function resetEntireSystemPreservingUsers(): void
    {
        $users = User::query()
            ->with('department.faculty.university')
            ->get()
            ->map(function (User $user): array {
                return [
                    'name' => $user->name,
                    'phone' => (string) $user->phone,
                    'role' => $user->role->value,
                    'department_name' => $user->department?->name,
                    'is_blocked' => (bool) $user->is_blocked,
                    'password' => $user->getAuthPassword(),
                    'remember_token' => $user->remember_token,
                ];
            })
            ->all();

        Artisan::call('migrate:fresh', ['--seed' => true, '--force' => true]);

        foreach ($users as $snapshot) {
            $departmentId = null;
            if (! empty($snapshot['department_name'])) {
                $departmentId = Department::query()
                    ->where('name', $snapshot['department_name'])
                    ->value('id');
            }

            User::query()->updateOrCreate(
                ['phone' => $snapshot['phone']],
                [
                    'name' => $snapshot['name'],
                    'role' => $snapshot['role'],
                    'department_id' => $snapshot['role'] === UserRole::Admin->value ? null : $departmentId,
                    'is_blocked' => $snapshot['is_blocked'],
                    'password' => $snapshot['password'],
                    'remember_token' => $snapshot['remember_token'],
                ]
            );
        }

        Cache::flush();
    }
}
