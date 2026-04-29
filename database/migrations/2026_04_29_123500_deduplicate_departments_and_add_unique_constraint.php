<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $duplicateGroups = DB::table('departments')
            ->selectRaw('faculty_id, LOWER(TRIM(name)) as normalized_name, COUNT(*) as duplicate_count')
            ->groupBy('faculty_id', DB::raw('LOWER(TRIM(name))'))
            ->havingRaw('COUNT(*) > 1')
            ->get();

        foreach ($duplicateGroups as $group) {
            $duplicates = DB::table('departments')
                ->select('id')
                ->where('faculty_id', $group->faculty_id)
                ->whereRaw('LOWER(TRIM(name)) = ?', [$group->normalized_name])
                ->orderBy('created_at')
                ->orderBy('id')
                ->get();

            if ($duplicates->count() < 2) {
                continue;
            }

            $keepId = $duplicates->first()->id;
            $removeIds = $duplicates->skip(1)->pluck('id')->all();

            foreach ($removeIds as $removeId) {
                $coursesInRemovedDepartment = DB::table('courses')
                    ->where('department_id', $removeId)
                    ->get(['id', 'code']);

                foreach ($coursesInRemovedDepartment as $course) {
                    $existingCourse = DB::table('courses')
                        ->where('department_id', $keepId)
                        ->whereRaw('LOWER(TRIM(code)) = LOWER(TRIM(?))', [$course->code])
                        ->first(['id']);

                    if ($existingCourse !== null) {
                        DB::table('exam_submissions')
                            ->where('course_id', $course->id)
                            ->update(['course_id' => $existingCourse->id]);

                        DB::table('courses')
                            ->where('id', $course->id)
                            ->delete();

                        continue;
                    }

                    DB::table('courses')
                        ->where('id', $course->id)
                        ->update(['department_id' => $keepId]);
                }
            }

            DB::table('users')
                ->whereIn('department_id', $removeIds)
                ->update(['department_id' => $keepId]);

            DB::table('departments')
                ->whereIn('id', $removeIds)
                ->delete();
        }

        Schema::table('departments', function (Blueprint $table) {
            $table->unique(['faculty_id', 'name'], 'departments_faculty_id_name_unique');
        });
    }

    public function down(): void
    {
        Schema::table('departments', function (Blueprint $table) {
            $table->dropUnique('departments_faculty_id_name_unique');
        });
    }
};
