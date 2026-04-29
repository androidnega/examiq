<?php

namespace Database\Seeders;

use App\Enums\UserRole;
use App\Models\Department;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $cs = Department::query()->where('name', 'Computer Science')->firstOrFail();

        $users = [
            [
                'name' => 'Dr. Kwame Mensah',
                'phone' => '0241000001',
                'role' => UserRole::Hod,
                'department_id' => $cs->id,
            ],
            [
                'name' => 'Mr. Kofi Asare',
                'phone' => '0242000001',
                'role' => UserRole::Lecturer,
                'department_id' => $cs->id,
            ],
            [
                'name' => 'Dr. Nana Adu',
                'phone' => '0243000001',
                'role' => UserRole::Moderator,
                'department_id' => $cs->id,
            ],
            [
                'name' => 'Mr. Daniel Tetteh',
                'phone' => '0244000000',
                'role' => UserRole::ExamOfficer,
                'department_id' => null,
            ],
        ];

        foreach ($users as $user) {
            User::query()->updateOrCreate(
                ['phone' => $user['phone']],
                [
                    'name' => $user['name'],
                    'role' => $user['role'],
                    'department_id' => $user['department_id'],
                    'is_blocked' => false,
                    'password' => Hash::make('password123'),
                ]
            );
        }
    }
}
