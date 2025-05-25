<?php

namespace App\Enum;

enum UserEnum:string
{
    case ADMIN = 'admin';
    case STUDENT = 'student';
    case TEACHER = 'teacher';

    public function caseToClass()
    {

    }
}
