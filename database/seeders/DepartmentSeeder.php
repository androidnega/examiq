<?php

namespace Database\Seeders;

use App\Models\Department;
use App\Models\Faculty;
use Illuminate\Database\Seeder;

class DepartmentSeeder extends Seeder
{
    public function run(): void
    {
        $appliedSciences = Faculty::query()->where('name', 'Faculty of Applied Sciences')->firstOrFail();
        $business = Faculty::query()->where('name', 'Faculty of Business Studies')->firstOrFail();
        $engineering = Faculty::query()->where('name', 'Faculty of Engineering')->firstOrFail();

        $departments = [
            ['faculty_id' => $appliedSciences->id, 'name' => 'Computer Science'],
            ['faculty_id' => $appliedSciences->id, 'name' => 'Mathematics, Statistics and Actuarial Science'],
            ['faculty_id' => $business->id, 'name' => 'Accountancy and Finance'],
            ['faculty_id' => $business->id, 'name' => 'Marketing Studies'],
            ['faculty_id' => $business->id, 'name' => 'Procurement and Supply Chain Management'],
            ['faculty_id' => $engineering->id, 'name' => 'Electrical/Electronic Engineering'],
            ['faculty_id' => $engineering->id, 'name' => 'Mechanical Engineering'],
            ['faculty_id' => $engineering->id, 'name' => 'Civil Engineering'],
        ];

        foreach ($departments as $dept) {
            Department::query()->create([
                'faculty_id' => $dept['faculty_id'],
                'name' => $dept['name'],
            ]);
        }
    }
}
