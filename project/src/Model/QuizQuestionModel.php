<?php

namespace App\Model;

use App\Entity\Quiz;
use App\Validator\IsResourceOwnedByLoggedInUser;
use Symfony\Component\Validator\Constraints as Assert;

class QuizQuestionModel
{

    #[Assert\NotNull]
    #[Assert\Type('string')]
    public $question = null;

    #[Assert\NotNull]
    #[Assert\Type('list')]
    #[Assert\Count(exactly: 4)]
    public $possibleAnswers = null;

    #[Assert\NotNull]
    #[Assert\Type('integer')]
    #[Assert\Range(min: 0, max: 3)]
    public $correctAnswerIndex = null;

    #[Assert\NotNull]
    #[Assert\Type(Quiz::class)]
    #[IsResourceOwnedByLoggedInUser]
    public $quiz = null;
}