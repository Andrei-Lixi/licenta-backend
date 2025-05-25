<?php

namespace App\Model;
use App\Validator\EntityExists;
use Symfony\Component\Validator\Constraints as Assert;

class QuizModel
{
    #[Assert\NotNull]
    #[Assert\Type('string')]
    public $name = null;

}