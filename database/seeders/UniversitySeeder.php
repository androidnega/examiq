<?php

namespace Database\Seeders;

use App\Models\University;
use Illuminate\Database\Seeder;

class UniversitySeeder extends Seeder
{
    public function run(): void
    {
        University::query()->firstOrCreate([
            'name' => 'Takoradi Technical University',
        ]);
    }
}
