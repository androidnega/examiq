<?php

namespace App\Policies;

use App\Enums\SubmissionStatus;
use App\Enums\UserRole;
use App\Models\ExamSubmission;
use App\Models\User;

class ExamSubmissionPolicy
{
    public function viewAny(User $user): bool
    {
        return in_array($user->role, [
            UserRole::Lecturer,
            UserRole::Hod,
            UserRole::Moderator,
            UserRole::ExamOfficer,
            UserRole::Admin,
        ], true);
    }

    public function view(User $user, ExamSubmission $examSubmission): bool
    {
        return match ($user->role) {
            UserRole::Admin => true,
            UserRole::Lecturer => $examSubmission->lecturer_id === $user->id,
            UserRole::Hod => $this->inHodDepartment($user, $examSubmission),
            UserRole::Moderator => ! in_array($examSubmission->status, [
                SubmissionStatus::Approved,
                SubmissionStatus::AwaitingHodApproval,
            ], true)
                && $examSubmission->moderationAssignments()
                    ->where('moderator_id', $user->id)
                    ->exists(),
            UserRole::ExamOfficer => $examSubmission->status === SubmissionStatus::Approved,
            default => false,
        };
    }

    public function create(User $user): bool
    {
        return $user->role === UserRole::Lecturer;
    }

    public function update(User $user, ExamSubmission $examSubmission): bool
    {
        return $user->role === UserRole::Lecturer
            && $examSubmission->lecturer_id === $user->id
            && $examSubmission->status === SubmissionStatus::Pending;
    }

    public function revise(User $user, ExamSubmission $examSubmission): bool
    {
        return $user->role === UserRole::Lecturer
            && $examSubmission->lecturer_id === $user->id
            && $examSubmission->status === SubmissionStatus::UnderRevision;
    }

    public function delete(User $user, ExamSubmission $examSubmission): bool
    {
        return false;
    }

    public function assignModerator(User $user, ExamSubmission $examSubmission): bool
    {
        return $user->role === UserRole::Hod
            && $user->department_id !== null
            && $this->inHodDepartment($user, $examSubmission);
    }

    public function review(User $user, ExamSubmission $examSubmission): bool
    {
        return $user->role === UserRole::Moderator
            && $examSubmission->status === SubmissionStatus::UnderReview
            && $examSubmission->moderationAssignments()
                ->where('moderator_id', $user->id)
                ->exists();
    }

    public function approve(User $user, ExamSubmission $examSubmission): bool
    {
        return $user->role === UserRole::Hod
            && $user->department_id !== null
            && $examSubmission->status === SubmissionStatus::AwaitingHodApproval
            && $this->inHodDepartment($user, $examSubmission);
    }

    public function reject(User $user, ExamSubmission $examSubmission): bool
    {
        return $user->role === UserRole::Hod
            && $user->department_id !== null
            && $examSubmission->status === SubmissionStatus::AwaitingHodApproval
            && $this->inHodDepartment($user, $examSubmission);
    }

    private function inHodDepartment(User $user, ExamSubmission $examSubmission): bool
    {
        $examSubmission->loadMissing('course');

        return $user->department_id !== null
            && $examSubmission->course !== null
            && $examSubmission->course->department_id === $user->department_id;
    }
}
