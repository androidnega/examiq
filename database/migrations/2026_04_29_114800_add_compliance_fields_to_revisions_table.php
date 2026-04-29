<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('revisions', function (Blueprint $table) {
            $table->boolean('received_moderated_questions')->default(false)->after('notes');
            $table->boolean('received_moderated_marking_scheme')->default(false)->after('received_moderated_questions');
            $table->boolean('received_course_outline')->default(false)->after('received_moderated_marking_scheme');
            $table->boolean('received_moderator_comment_sheet')->default(false)->after('received_course_outline');
            $table->date('received_from_moderator_on')->nullable()->after('received_moderator_comment_sheet');
            $table->string('moderator_general_comment', 32)->nullable()->after('received_from_moderator_on');
            $table->text('response_action_taken')->nullable()->after('moderator_general_comment');
        });
    }

    public function down(): void
    {
        Schema::table('revisions', function (Blueprint $table) {
            $table->dropColumn([
                'received_moderated_questions',
                'received_moderated_marking_scheme',
                'received_course_outline',
                'received_moderator_comment_sheet',
                'received_from_moderator_on',
                'moderator_general_comment',
                'response_action_taken',
            ]);
        });
    }
};
