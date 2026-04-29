<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('moderations', function (Blueprint $table) {
            for ($i = 1; $i <= 11; $i++) {
                $table->char("rubric_{$i}_grade", 1)->nullable()->after('feedback');
            }

            $table->text('recommend_accept_questions')->nullable()->after('rubric_11_grade');
            $table->text('recommend_reject_questions')->nullable()->after('recommend_accept_questions');
            $table->text('recommend_reset_questions')->nullable()->after('recommend_reject_questions');
            $table->text('question_paper_comments')->nullable()->after('recommend_reset_questions');
            $table->text('marking_scheme_comments')->nullable()->after('question_paper_comments');
            $table->string('question_paper_assessment', 32)->nullable()->after('marking_scheme_comments');
            $table->string('marking_scheme_assessment', 32)->nullable()->after('question_paper_assessment');
            $table->string('overall_rating', 32)->nullable()->after('marking_scheme_assessment');
            $table->text('improvement_comments')->nullable()->after('overall_rating');
            $table->date('moderated_on')->nullable()->after('improvement_comments');
        });
    }

    public function down(): void
    {
        Schema::table('moderations', function (Blueprint $table) {
            $columns = [];
            for ($i = 1; $i <= 11; $i++) {
                $columns[] = "rubric_{$i}_grade";
            }
            $columns = array_merge($columns, [
                'recommend_accept_questions',
                'recommend_reject_questions',
                'recommend_reset_questions',
                'question_paper_comments',
                'marking_scheme_comments',
                'question_paper_assessment',
                'marking_scheme_assessment',
                'overall_rating',
                'improvement_comments',
                'moderated_on',
            ]);

            $table->dropColumn($columns);
        });
    }
};
