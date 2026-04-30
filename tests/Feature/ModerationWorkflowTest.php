<?php

namespace Tests\Feature;

use App\Enums\SubmissionStatus;
use App\Enums\UserRole;
use App\Models\Course;
use App\Models\Department;
use App\Models\ExamSubmission;
use App\Models\Faculty;
use App\Models\ModerationAssignment;
use App\Models\Revision;
use App\Models\SubmissionFile;
use App\Models\University;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ModerationWorkflowTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @return array{hod: User, moderator: User, otherModerator: User, lecturer: User, course: Course}
     */
    private function seedDepartmentScenario(): array
    {
        $university = University::query()->create(['name' => 'U']);
        $faculty = Faculty::query()->create(['university_id' => $university->id, 'name' => 'F']);
        $department = Department::query()->create(['faculty_id' => $faculty->id, 'name' => 'D']);

        $hod = User::factory()->create([
            'role' => UserRole::Hod,
            'department_id' => $department->id,
        ]);
        $moderator = User::factory()->create([
            'role' => UserRole::Moderator,
            'department_id' => $department->id,
        ]);
        $otherModerator = User::factory()->create([
            'role' => UserRole::Moderator,
            'department_id' => $department->id,
        ]);
        $lecturer = User::factory()->create([
            'role' => UserRole::Lecturer,
            'department_id' => $department->id,
        ]);
        $course = Course::query()->create([
            'department_id' => $department->id,
            'name' => 'Course',
            'code' => 'C101',
            'level' => '100',
            'program' => 'P',
            'semester' => 'first',
        ]);

        return [
            'hod' => $hod,
            'moderator' => $moderator,
            'otherModerator' => $otherModerator,
            'lecturer' => $lecturer,
            'course' => $course,
        ];
    }

    private function pdfFile(string $name = 'exam.pdf', int $kilobytes = 64): UploadedFile
    {
        $padding = str_repeat('0', max(0, $kilobytes * 1024 - 32));

        return UploadedFile::fake()->createWithContent($name, "%PDF-1.4\n".$padding."\n%%EOF\n");
    }

    private function moderationPayload(string $status, ?string $feedback = null): array
    {
        return [
            'status' => $status,
            'feedback' => $feedback,
            'rubric_1_grade' => 'B',
            'rubric_2_grade' => 'B',
            'rubric_3_grade' => 'B',
            'rubric_4_grade' => 'B',
            'rubric_5_grade' => 'B',
            'rubric_6_grade' => 'B',
            'rubric_7_grade' => 'B',
            'rubric_8_grade' => 'B',
            'rubric_9_grade' => 'B',
            'rubric_10_grade' => 'B',
            'rubric_11_grade' => 'B',
            'question_count_section_a' => 2,
            'question_count_section_b' => 3,
            'question_count_section_c' => 1,
            'paper_duration' => '2.5 Hrs',
            'recommend_accept_questions' => 'All',
            'recommend_reject_questions' => '',
            'recommend_reset_questions' => '',
            'question_paper_comments' => 'Section numbering updated.',
            'marking_scheme_comments' => 'Aligned with question sections.',
            'question_paper_assessment' => 'accepted_minor_corrections',
            'question_paper_assessments' => ['accepted_minor_corrections'],
            'marking_scheme_assessment' => 'accepted_all',
            'marking_scheme_assessments' => ['accepted_all'],
            'overall_rating' => 'very_good',
            'improvement_comments' => 'Good overall.',
            'moderated_on' => '2026-04-29',
            'moderator_signature_name' => 'Moderator User',
        ];
    }

    public function test_hod_assigns_moderator_and_review_updates_submission_status(): void
    {
        $s = $this->seedDepartmentScenario();

        $examSubmission = ExamSubmission::query()->create([
            'lecturer_id' => $s['lecturer']->id,
            'course_id' => $s['course']->id,
            'academic_year' => '2025/2026',
            'semester' => 'first',
            'students_count' => 30,
            'status' => SubmissionStatus::Pending,
            'current_version' => 1,
        ]);

        $this->actingAs($s['hod'])->post(route('dashboard.department.assign-moderators'), [
            'submission_ids' => [$examSubmission->id],
            'moderator_ids' => [$s['moderator']->id],
        ])->assertRedirect(route('dashboard.department.index'));

        $examSubmission->refresh();
        $this->assertTrue($examSubmission->status === SubmissionStatus::UnderReview);
        $this->assertTrue(ModerationAssignment::query()->where('exam_submission_id', $examSubmission->id)->where('moderator_id', $s['moderator']->id)->exists());

        $this->actingAs($s['moderator'])->get(route('dashboard.reviews.index'))->assertOk();
        $this->actingAs($s['moderator'])->get(route('dashboard.reviews.show', $examSubmission))->assertOk();

        $this->actingAs($s['moderator'])
            ->post(route('dashboard.reviews.store', $examSubmission), $this->moderationPayload('accepted'))
            ->assertRedirect(route('dashboard.reviews.show', $examSubmission));

        $examSubmission->refresh();
        $this->assertTrue($examSubmission->status === SubmissionStatus::AwaitingHodApproval);

        $this->actingAs($s['moderator'])
            ->get(route('dashboard.reviews.show', $examSubmission))
            ->assertForbidden();

        $this->actingAs($s['hod'])->get(route('dashboard.approvals.index'))->assertOk();

        $this->actingAs($s['hod'])
            ->post(route('dashboard.department.approve', $examSubmission))
            ->assertRedirect(route('dashboard.department.show', $examSubmission));

        $examSubmission->refresh();
        $this->assertTrue($examSubmission->status === SubmissionStatus::Approved);
    }

    public function test_minor_changes_sets_under_revision(): void
    {
        $s = $this->seedDepartmentScenario();

        $examSubmission = ExamSubmission::query()->create([
            'lecturer_id' => $s['lecturer']->id,
            'course_id' => $s['course']->id,
            'academic_year' => '2025/2026',
            'semester' => 'first',
            'students_count' => 10,
            'status' => SubmissionStatus::UnderReview,
            'current_version' => 1,
        ]);

        ModerationAssignment::query()->create([
            'exam_submission_id' => $examSubmission->id,
            'moderator_id' => $s['moderator']->id,
            'assigned_by' => $s['hod']->id,
            'assigned_at' => now(),
        ]);

        $this->actingAs($s['moderator'])
            ->post(route('dashboard.reviews.store', $examSubmission), $this->moderationPayload('minor_changes', 'Please adjust section 2.'))
            ->assertRedirect(route('dashboard.reviews.show', $examSubmission));

        $examSubmission->refresh();
        $this->assertTrue($examSubmission->status === SubmissionStatus::UnderRevision);
    }

    public function test_moderator_cannot_open_unassigned_submission(): void
    {
        $s = $this->seedDepartmentScenario();

        $examSubmission = ExamSubmission::query()->create([
            'lecturer_id' => $s['lecturer']->id,
            'course_id' => $s['course']->id,
            'academic_year' => '2025/2026',
            'semester' => 'first',
            'students_count' => 5,
            'status' => SubmissionStatus::Pending,
            'current_version' => 1,
        ]);

        ModerationAssignment::query()->create([
            'exam_submission_id' => $examSubmission->id,
            'moderator_id' => $s['moderator']->id,
            'assigned_by' => $s['hod']->id,
            'assigned_at' => now(),
        ]);

        $this->actingAs($s['otherModerator'])
            ->get(route('dashboard.reviews.show', $examSubmission))
            ->assertForbidden();
    }

    public function test_lecturer_revision_then_hod_approval_completes_cycle(): void
    {
        Storage::fake('private');
        $s = $this->seedDepartmentScenario();

        $this->actingAs($s['lecturer'])->post(route('dashboard.submissions.store'), [
            'course_id' => $s['course']->id,
            'academic_year' => '2025/2026',
            'semester' => 'first',
            'students_count' => 12,
            'file_questions' => $this->pdfFile('q1.pdf'),
            'file_marking_scheme' => $this->pdfFile('m1.pdf'),
            'file_outline' => $this->pdfFile('o1.pdf'),
        ])->assertRedirect();

        $examSubmission = ExamSubmission::query()->firstOrFail();
        $this->assertSame(1, $examSubmission->current_version);

        $this->actingAs($s['hod'])->post(route('dashboard.department.assign-moderators'), [
            'submission_ids' => [$examSubmission->id],
            'moderator_ids' => [$s['moderator']->id],
        ])->assertRedirect(route('dashboard.department.index'));

        $examSubmission->refresh();
        $this->assertTrue($examSubmission->status === SubmissionStatus::UnderReview);

        $this->actingAs($s['moderator'])
            ->post(route('dashboard.reviews.store', $examSubmission), $this->moderationPayload('minor_changes', 'Adjust question 4.'))
            ->assertRedirect(route('dashboard.reviews.show', $examSubmission));

        $examSubmission->refresh();
        $this->assertTrue($examSubmission->status === SubmissionStatus::UnderRevision);

        $this->actingAs($s['lecturer'])->post(route('dashboard.submissions.revise.update', $examSubmission), [
            '_method' => 'PUT',
            'academic_year' => '2025/2026',
            'semester' => 'first',
            'students_count' => 12,
            'revision_notes' => 'Rewrote question 4 and updated rubric.',
            'received_moderated_questions' => '1',
            'received_moderated_marking_scheme' => '1',
            'received_course_outline' => '1',
            'received_moderator_comment_sheet' => '1',
            'received_from_moderator_on' => '2026-04-29',
            'moderator_general_comment' => 'with_modifications',
            'response_action_taken' => 'Adjusted question four and aligned marking guide.',
            'file_questions' => $this->pdfFile('q2.pdf'),
            'file_marking_scheme' => $this->pdfFile('m2.pdf'),
            'file_outline' => $this->pdfFile('o2.pdf'),
        ])->assertRedirect(route('dashboard.submissions.show', $examSubmission));

        $examSubmission->refresh();
        $this->assertSame(2, $examSubmission->current_version);
        $this->assertTrue($examSubmission->status === SubmissionStatus::AwaitingHodApproval);
        $this->assertTrue(Revision::query()->where('submission_id', $examSubmission->id)->exists());
        $this->assertSame(6, SubmissionFile::query()->where('submission_id', $examSubmission->id)->count());

        $this->actingAs($s['hod'])
            ->post(route('dashboard.department.approve', $examSubmission))
            ->assertRedirect(route('dashboard.department.show', $examSubmission));

        $examSubmission->refresh();
        $this->assertTrue($examSubmission->status === SubmissionStatus::Approved);
    }
}
