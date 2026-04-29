<?php

namespace Tests\Feature;

use App\Enums\SubmissionStatus;
use App\Enums\UserRole;
use App\Models\Course;
use App\Models\Department;
use App\Models\ExamSubmission;
use App\Models\Faculty;
use App\Models\SubmissionFile;
use App\Models\University;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class LecturerSubmissionFlowTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @return array{0: User, 1: Course}
     */
    private function lecturerWithCourse(): array
    {
        $university = University::query()->create(['name' => 'Test University']);
        $faculty = Faculty::query()->create([
            'university_id' => $university->id,
            'name' => 'Test Faculty',
        ]);
        $department = Department::query()->create([
            'faculty_id' => $faculty->id,
            'name' => 'Test Department',
        ]);
        $lecturer = User::factory()->create([
            'role' => UserRole::Lecturer,
            'department_id' => $department->id,
        ]);
        $course = Course::query()->create([
            'department_id' => $department->id,
            'name' => 'Test Course',
            'code' => 'TC101',
            'level' => '100',
            'program' => 'BSc Test',
            'semester' => 'first',
        ]);

        return [$lecturer, $course];
    }

    private function pdfFile(string $name = 'exam.pdf', int $kilobytes = 64): UploadedFile
    {
        $padding = str_repeat('0', max(0, $kilobytes * 1024 - 32));

        return UploadedFile::fake()->createWithContent($name, "%PDF-1.4\n".$padding."\n%%EOF\n");
    }

    public function test_lecturer_can_create_submission_with_required_pdfs(): void
    {
        Storage::fake('private');
        [$lecturer, $course] = $this->lecturerWithCourse();

        $response = $this->actingAs($lecturer)->post(route('dashboard.submissions.store'), [
            'course_id' => $course->id,
            'academic_year' => '2025/2026',
            'semester' => 'first',
            'students_count' => 42,
            'file_questions' => $this->pdfFile('questions.pdf'),
            'file_marking_scheme' => $this->pdfFile('marking.pdf'),
            'file_outline' => $this->pdfFile('outline.pdf'),
        ]);

        $response->assertRedirect(route('dashboard.submissions.show', ExamSubmission::query()->firstOrFail()));

        $submission = ExamSubmission::query()->firstOrFail();
        $this->assertSame(1, $submission->current_version);
        $this->assertTrue($submission->status === SubmissionStatus::Pending);

        $files = SubmissionFile::query()->where('submission_id', $submission->id)->get();
        $this->assertCount(3, $files);
        foreach ($files as $file) {
            Storage::disk('private')->assertExists($file->file_path);
        }
    }

    public function test_rejects_file_larger_than_one_megabyte(): void
    {
        Storage::fake('private');
        [$lecturer, $course] = $this->lecturerWithCourse();

        $response = $this->actingAs($lecturer)->post(route('dashboard.submissions.store'), [
            'course_id' => $course->id,
            'academic_year' => '2025/2026',
            'semester' => 'first',
            'students_count' => 10,
            'file_questions' => $this->pdfFile('big.pdf', 1025),
            'file_marking_scheme' => $this->pdfFile('m.pdf'),
            'file_outline' => $this->pdfFile('o.pdf'),
        ]);

        $response->assertSessionHasErrors(['file_questions']);
        $this->assertSame(0, ExamSubmission::query()->count());
    }

    public function test_update_increments_version_and_retains_previous_files(): void
    {
        Storage::fake('private');
        [$lecturer, $course] = $this->lecturerWithCourse();

        $this->actingAs($lecturer)->post(route('dashboard.submissions.store'), [
            'course_id' => $course->id,
            'academic_year' => '2025/2026',
            'semester' => 'first',
            'students_count' => 10,
            'file_questions' => $this->pdfFile('q1.pdf'),
            'file_marking_scheme' => $this->pdfFile('m1.pdf'),
            'file_outline' => $this->pdfFile('o1.pdf'),
        ]);

        $submission = ExamSubmission::query()->firstOrFail();

        $this->actingAs($lecturer)->post(route('dashboard.submissions.update', $submission), [
            '_method' => 'PUT',
            'academic_year' => '2026/2027',
            'semester' => 'second',
            'students_count' => 20,
            'file_questions' => $this->pdfFile('q2.pdf'),
            'file_marking_scheme' => $this->pdfFile('m2.pdf'),
            'file_outline' => $this->pdfFile('o2.pdf'),
        ])->assertRedirect(route('dashboard.submissions.show', $submission));

        $submission->refresh();
        $this->assertSame(2, $submission->current_version);
        $this->assertTrue($submission->status === SubmissionStatus::Pending);
        $this->assertSame(6, SubmissionFile::query()->where('submission_id', $submission->id)->count());
        $this->assertTrue(SubmissionFile::query()->where('submission_id', $submission->id)->where('version', 1)->exists());
        $this->assertTrue(SubmissionFile::query()->where('submission_id', $submission->id)->where('version', 2)->exists());
    }

    public function test_cannot_open_revision_form_when_submission_is_approved(): void
    {
        Storage::fake('private');
        [$lecturer, $course] = $this->lecturerWithCourse();

        $this->actingAs($lecturer)->post(route('dashboard.submissions.store'), [
            'course_id' => $course->id,
            'academic_year' => '2025/2026',
            'semester' => 'first',
            'students_count' => 5,
            'file_questions' => $this->pdfFile(),
            'file_marking_scheme' => $this->pdfFile('m.pdf'),
            'file_outline' => $this->pdfFile('o.pdf'),
        ]);

        $submission = ExamSubmission::query()->firstOrFail();
        $submission->update(['status' => SubmissionStatus::Approved]);

        $this->actingAs($lecturer)
            ->get(route('dashboard.submissions.edit', $submission))
            ->assertForbidden();
    }

    public function test_cannot_open_pending_amend_route_when_submission_is_approved(): void
    {
        Storage::fake('private');
        [$lecturer, $course] = $this->lecturerWithCourse();

        $this->actingAs($lecturer)->post(route('dashboard.submissions.store'), [
            'course_id' => $course->id,
            'academic_year' => '2025/2026',
            'semester' => 'first',
            'students_count' => 5,
            'file_questions' => $this->pdfFile(),
            'file_marking_scheme' => $this->pdfFile('m.pdf'),
            'file_outline' => $this->pdfFile('o.pdf'),
        ]);

        $submission = ExamSubmission::query()->firstOrFail();
        $submission->update(['status' => SubmissionStatus::Approved]);

        $this->actingAs($lecturer)
            ->get(route('dashboard.submissions.update.edit', $submission))
            ->assertForbidden();
    }
}
