<?php

namespace App\Constant;

use App\Entity\UserExtension\AdminUser;
use App\Entity\UserExtension\StudentUser;
use App\Entity\UserExtension\TeacherUser;

class FixtureData
{
    const USER_DATA = [
        [
            'email' => 'email1@fakemail.com',
            'password' => 'parola',
            'name' => 'Popescu Ion',
            'school' => 'Pia Bratianu',
            'class' => StudentUser::class
        ],
        [
            'email' => 'email2@fakemail.com',
            'password' => 'parola',
            'name' => 'Lixandru Andrei',
            'school' => 'Pia Bratianu',
            'class' => StudentUser::class
        ],
        [
            'email' => 'teacher1@fakemail.com',
            'password' => 'parola',
            'name' => 'Anton Marian',
            'school' => 'Pedagogic',
            'class' => TeacherUser::class
        ],
        [
            'email' => 'teacher2@fakemail.com',
            'password' => 'parola',
            'name' => 'Ion Ion',
            'school' => 'Sportiv',
            'class' => TeacherUser::class
        ],
        [
            'email' => 'admin1@fakemail.com',
            'password' => 'parola',
            'name' => 'Admin Principal',
            'school' => 'Inspectorat',
            'class' => AdminUser::class
        ]
    ];

    const LESSON_DATA = [
        [
            ''
        ]
    ];

    const QUIZ_DATA = [
        [

        ]
    ];

}