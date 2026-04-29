<?php

namespace Database\Seeders;

use App\Models\Faculty;
use App\Models\University;
use Illuminate\Database\Seeder;

class FacultySeeder extends Seeder
{
    public function run(): void
    {
        $ttu = University::query()->firstOrFail();

        $faculties = [
            'Faculty of Applied Arts and Technology',
            'Faculty of Applied Sciences',
            'Faculty of Business Studies',
            'Faculty of Built and Natural Environment',
            'Faculty of Engineering',
            'Faculty of Health and Allied Sciences',
            'Faculty of Maritime and Nautical Studies',
        ];

        foreach ($faculties as $name) {
            Faculty::query()->firstOrCreate([
                'university_id' => $ttu->id,
                'name' => $name,
            ]);
        }
    }
}
