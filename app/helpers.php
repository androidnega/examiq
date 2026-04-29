<?php

/**
 * Minimum invigilators required (one per 40 students, rounded up).
 */
function invigilators_required(int $studentsCount): int
{
    if ($studentsCount <= 0) {
        return 0;
    }

    return (int) ceil($studentsCount / 40);
}
