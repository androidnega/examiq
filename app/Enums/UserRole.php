<?php

namespace App\Enums;

enum UserRole: string
{
    case Admin = 'admin';
    case Hod = 'hod';
    case Lecturer = 'lecturer';
    case Moderator = 'moderator';
    case ExamOfficer = 'exam_officer';
}
