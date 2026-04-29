<?php

namespace Database\Seeders;

use App\Models\Course;
use App\Models\Department;
use Illuminate\Database\Seeder;

class CourseSeeder extends Seeder
{
    public function run(): void
    {
        $cs = Department::query()->where('name', 'Computer Science')->firstOrFail();
        $math = Department::query()->where('name', 'Mathematics, Statistics and Actuarial Science')->firstOrFail();
        $eee = Department::query()->where('name', 'Electrical/Electronic Engineering')->firstOrFail();

        $courses = [
            ['department_id' => $cs->id, 'name' => 'Principles of Programming', 'code' => 'CS101', 'level' => '100', 'program' => 'BSc Computer Science', 'semester' => 'first'],
            ['department_id' => $cs->id, 'name' => 'Database Management Systems (Oracle)', 'code' => 'CS203', 'level' => '200', 'program' => 'BSc Computer Science', 'semester' => 'first'],
            ['department_id' => $cs->id, 'name' => 'Web Development using PHP', 'code' => 'CS205', 'level' => '200', 'program' => 'BSc Computer Science', 'semester' => 'second'],
            ['department_id' => $cs->id, 'name' => 'Computer Architecture and Organization', 'code' => 'CS207', 'level' => '200', 'program' => 'BSc Computer Science', 'semester' => 'first'],
            ['department_id' => $cs->id, 'name' => 'Visual Basic .NET Programming', 'code' => 'CS209', 'level' => '200', 'program' => 'BSc Computer Science', 'semester' => 'second'],
            ['department_id' => $math->id, 'name' => 'Discrete Mathematics', 'code' => 'MTH101', 'level' => '100', 'program' => 'BSc Mathematics', 'semester' => 'first'],
            ['department_id' => $math->id, 'name' => 'Probability and Statistics', 'code' => 'MTH203', 'level' => '200', 'program' => 'BSc Mathematics', 'semester' => 'first'],
            ['department_id' => $eee->id, 'name' => 'Electrical Machines', 'code' => 'EEE301', 'level' => '300', 'program' => 'BSc Electrical/Electronic Engineering', 'semester' => 'first'],
            ['department_id' => $eee->id, 'name' => 'Circuit Analysis', 'code' => 'EEE201', 'level' => '200', 'program' => 'BSc Electrical/Electronic Engineering', 'semester' => 'first'],
        ];

        foreach ($courses as $course) {
            Course::query()->updateOrCreate(
                [
                    'department_id' => $course['department_id'],
                    'code' => $course['code'],
                ],
                $course
            );
        }
    }
}
