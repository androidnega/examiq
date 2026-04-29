<?php

namespace App\Models;

use App\Enums\UserRole;
use App\Models\Concerns\HasUuid;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

#[Fillable([
    'name',
    'phone',
    'role',
    'department_id',
    'is_blocked',
    'password',
])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, HasUuid, Notifiable;

    protected static function booted(): void
    {
        static::saving(function (self $user): void {
            if ($user->role === UserRole::Admin) {
                $user->department_id = null;
            }
        });
    }

    protected function casts(): array
    {
        return [
            'is_blocked' => 'boolean',
            'password' => 'hashed',
            'role' => UserRole::class,
        ];
    }

    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }

    public function examSubmissions(): HasMany
    {
        return $this->hasMany(ExamSubmission::class, 'lecturer_id');
    }

    public function moderations(): HasMany
    {
        return $this->hasMany(Moderation::class, 'moderator_id');
    }

    public function moderationAssignments(): HasMany
    {
        return $this->hasMany(ModerationAssignment::class, 'moderator_id');
    }

    public function isSuperAdmin(): bool
    {
        if ($this->role !== UserRole::Admin) {
            return false;
        }

        $allowedPhones = config('examiq.super_admin_phones', []);

        return in_array((string) $this->phone, $allowedPhones, true);
    }
}
