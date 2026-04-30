<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('moderations', function (Blueprint $table): void {
            $table->unsignedInteger('question_count_section_a')->nullable()->after('rubric_11_grade');
            $table->unsignedInteger('question_count_section_b')->nullable()->after('question_count_section_a');
            $table->unsignedInteger('question_count_section_c')->nullable()->after('question_count_section_b');
            $table->string('paper_duration', 64)->nullable()->after('question_count_section_c');
            $table->text('question_paper_assessments')->nullable()->after('question_paper_assessment');
            $table->text('marking_scheme_assessments')->nullable()->after('marking_scheme_assessment');
            $table->string('moderator_signature_name')->nullable()->after('moderated_on');
        });
    }

    public function down(): void
    {
        Schema::table('moderations', function (Blueprint $table): void {
            $table->dropColumn([
                'question_count_section_a',
                'question_count_section_b',
                'question_count_section_c',
                'paper_duration',
                'question_paper_assessments',
                'marking_scheme_assessments',
                'moderator_signature_name',
            ]);
        });
    }
};
