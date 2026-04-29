<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $duplicateGroups = DB::table('faculties')
            ->selectRaw('university_id, LOWER(TRIM(name)) as normalized_name, COUNT(*) as duplicate_count')
            ->groupBy('university_id', DB::raw('LOWER(TRIM(name))'))
            ->havingRaw('COUNT(*) > 1')
            ->get();

        foreach ($duplicateGroups as $group) {
            $duplicates = DB::table('faculties')
                ->select('id')
                ->where('university_id', $group->university_id)
                ->whereRaw('LOWER(TRIM(name)) = ?', [$group->normalized_name])
                ->orderBy('created_at')
                ->orderBy('id')
                ->get();

            if ($duplicates->count() < 2) {
                continue;
            }

            $keepId = $duplicates->first()->id;
            $removeIds = $duplicates->skip(1)->pluck('id')->all();

            DB::table('departments')
                ->whereIn('faculty_id', $removeIds)
                ->update(['faculty_id' => $keepId]);

            DB::table('faculties')
                ->whereIn('id', $removeIds)
                ->delete();
        }

        Schema::table('faculties', function (Blueprint $table) {
            $table->unique(['university_id', 'name'], 'faculties_university_id_name_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('faculties', function (Blueprint $table) {
            $table->dropUnique('faculties_university_id_name_unique');
        });
    }
};
