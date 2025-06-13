<?php

namespace App\Model;

use App\Entity\UserExtension\TeacherUser;
use App\Enum\FieldEnum;
use App\Enum\GradeEnum;
use App\Validator\EntityExists;
use Symfony\Component\Validator\Constraints as Assert;

class LessonModel
{
    #[Assert\NotNull()]
    #[Assert\Type('string')]
    public $name = null;

    #[Assert\NotNull()]
    #[Assert\Type('string')]
    public $grade = null;

    #[Assert\NotNull()]
    #[Assert\Type('string')]
    public $field = null;

    #[Assert\IsTrue(message: "Invalid grade")]
    public function isGradeValid() : bool
    {
        return in_array($this->grade , array_column(GradeEnum::cases(), 'value'));
    }

    #[Assert\IsTrue(message: "Invalid field")]
    public function isFieldValid() : bool
    {
        return in_array($this->field, array_column(FieldEnum::cases(), 'value'));
    }

}