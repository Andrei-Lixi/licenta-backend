<?php

namespace App\Model;

use App\Entity\UserExtension\TeacherUser;
use App\Validator\EntityExists;
use Symfony\Component\Validator\Constraints as Assert;

class LessonModel
{
    #[Assert\NotNull()]
    #[Assert\Type('string')]
    public $name = null;

}