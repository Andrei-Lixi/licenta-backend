<?php

namespace App\Constant;

use App\Entity\UserExtension\AdminUser;
use App\Entity\UserExtension\StudentUser;
use App\Entity\UserExtension\TeacherUser;
use App\Enum\UserEnum;

class UserData
{
    const DATA = [
        UserEnum::ADMIN->value => ['class' => AdminUser::class],
        UserEnum::TEACHER->value => ['class' => TeacherUser::class],
        UserEnum::STUDENT->value => ['class' => StudentUser::class]
    ];
}