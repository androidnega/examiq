<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('moderation_assignments', function (Blueprint $table): void {
            $table->uuid('id')->primary();
            $table->foreignUuid('exam_submission_id')->constrained('exam_submissions')->cascadeOnDelete();
            $table->foreignUuid('moderator_id')->constrained('users')->cascadeOnDelete();
            $table->foreignUuid('assigned_by')->constrained('users')->cascadeOnDelete();
            $table->timestamp('assigned_at')->useCurrent();
            $table->timestamps();

            $table->unique(['exam_submission_id', 'moderator_id']);
            $table->index('moderator_id');
            $table->index('exam_submission_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('moderation_assignments');
    }
};
