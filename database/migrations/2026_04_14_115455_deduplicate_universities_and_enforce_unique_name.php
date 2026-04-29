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
        $duplicateGroups = DB::table('universities')
            ->selectRaw('LOWER(TRIM(name)) as normalized_name, COUNT(*) as duplicate_count')
            ->groupBy(DB::raw('LOWER(TRIM(name))'))
            ->havingRaw('COUNT(*) > 1')
            ->get();

        foreach ($duplicateGroups as $group) {
            $duplicates = DB::table('universities')
                ->select('id')
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
                $faculties = DB::table('faculties')
                    ->where('university_id', $removeId)
                    ->get(['id', 'name']);

                foreach ($faculties as $faculty) {
                    $existing = DB::table('faculties')
                        ->where('university_id', $keepId)
                        ->whereRaw('LOWER(TRIM(name)) = LOWER(TRIM(?))', [$faculty->name])
                        ->first(['id']);

                    if ($existing) {
                        DB::table('departments')
                            ->where('faculty_id', $faculty->id)
                            ->update(['faculty_id' => $existing->id]);

                        DB::table('faculties')
                            ->where('id', $faculty->id)
                            ->delete();

                        continue;
                    }

                    DB::table('faculties')
                        ->where('id', $faculty->id)
                        ->update(['university_id' => $keepId]);
                }
            }

            DB::table('universities')
                ->whereIn('id', $removeIds)
                ->delete();
        }

        Schema::table('universities', function (Blueprint $table) {
            $table->unique('name', 'universities_name_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('universities', function (Blueprint $table) {
            $table->dropUnique('universities_name_unique');
        });
    }
};
