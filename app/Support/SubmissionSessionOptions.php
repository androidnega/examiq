<?php

namespace App\Support;

use Illuminate\Support\Facades\Cache;

class SubmissionSessionOptions
{
    public static function academicYears(): array
    {
        $defaults = (array) config('examiq.submission_academic_year_options', ['2025/2026']);
        $stored = Cache::get('examiq.submission_academic_year_options', $defaults);

        return self::normalize($stored, $defaults);
    }

    public static function semesters(): array
    {
        $defaults = (array) config('examiq.submission_semester_options', ['first', 'second']);
        $stored = Cache::get('examiq.submission_semester_options', $defaults);

        return self::normalize($stored, $defaults);
    }

    public static function setAcademicYears(array $values): void
    {
        Cache::forever('examiq.submission_academic_year_options', self::normalize($values, ['2025/2026']));
    }

    public static function setSemesters(array $values): void
    {
        Cache::forever('examiq.submission_semester_options', self::normalize($values, ['first', 'second']));
    }

    /**
     * @param  mixed  $values
     * @param  array<int, string>  $fallback
     * @return array<int, string>
     */
    private static function normalize(mixed $values, array $fallback): array
    {
        $normalized = array_values(array_unique(array_filter(array_map(
            static fn (mixed $item): string => trim((string) $item),
            is_array($values) ? $values : []
        ))));

        return $normalized !== [] ? $normalized : $fallback;
    }
}
