<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('courses', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('department_id')->constrained('departments')->cascadeOnDelete();
            $table->string('name');
            $table->string('code');
            $table->string('level')->nullable();
            $table->string('program')->nullable();
            $table->string('semester')->nullable();
            $table->timestamps();

            $table->unique(['department_id', 'code']);
            $table->index('department_id');
        });

        Schema::create('exam_submissions', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('lecturer_id')->constrained('users')->cascadeOnDelete();
            $table->foreignUuid('course_id')->constrained('courses')->cascadeOnDelete();
            $table->string('academic_year');
            $table->string('semester');
            $table->unsignedInteger('students_count')->default(0);
            $table->string('status', 32)->default('pending');
            $table->unsignedInteger('current_version')->default(1);
            $table->timestamps();

            $table->index('lecturer_id');
            $table->index('course_id');
            $table->index('status');
        });

        Schema::create('submission_files', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('submission_id')->constrained('exam_submissions')->cascadeOnDelete();
            $table->unsignedInteger('version');
            $table->string('type', 32);
            $table->string('file_path');
            $table->timestamps();

            $table->index(['submission_id', 'version']);
            $table->unique(['submission_id', 'version', 'type']);
        });

        Schema::create('moderations', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('submission_id')->constrained('exam_submissions')->cascadeOnDelete();
            $table->foreignUuid('moderator_id')->constrained('users')->cascadeOnDelete();
            $table->string('status', 32);
            $table->text('feedback')->nullable();
            $table->timestamps();

            $table->index('submission_id');
            $table->index('moderator_id');
        });

        Schema::create('revisions', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('submission_id')->constrained('exam_submissions')->cascadeOnDelete();
            $table->foreignUuid('lecturer_id')->constrained('users')->cascadeOnDelete();
            $table->text('notes');
            $table->timestamps();

            $table->index('submission_id');
        });

        Schema::create('activity_logs', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('action');
            $table->string('ip_address', 45)->nullable();
            $table->json('metadata')->nullable();
            $table->timestamp('created_at')->useCurrent();

            $table->index('user_id');
            $table->index('action');
        });

        Schema::create('otps', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('phone', 32)->index();
            $table->string('code');
            $table->timestamp('expires_at');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('otps');
        Schema::dropIfExists('activity_logs');
        Schema::dropIfExists('revisions');
        Schema::dropIfExists('moderations');
        Schema::dropIfExists('submission_files');
        Schema::dropIfExists('exam_submissions');
        Schema::dropIfExists('courses');
    }
};
