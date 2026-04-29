<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            UniversitySeeder::class,
            FacultySeeder::class,
            DepartmentSeeder::class,
            CourseSeeder::class,
            UserSeeder::class,
            ExamSubmissionSeeder::class,
            ModerationSeeder::class,
            RevisionSeeder::class,
        ]);
    }
}
