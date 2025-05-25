<?php

namespace App\Entity;

use App\Interface\OwnedResourceInterface;
use App\Repository\QuizQuestionRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Serializer\Attribute\Groups;

#[ORM\Entity(repositoryClass: QuizQuestionRepository::class)]
class QuizQuestion implements OwnedResourceInterface
{
    #[Groups(['quiz', 'quiz_question'])]
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[Groups(['quiz_question'])]
    #[ORM\Column(length: 255)]
    private ?string $question = null;

    #[Groups(['quiz_question'])]
    #[ORM\Column(type: Types::ARRAY)]
    private array $possibleAnswers = [];

    #[Groups(['quiz_question'])]
    #[ORM\Column]
    private ?int $correctAnswerIndex = null;

    #[ORM\ManyToOne(inversedBy: 'quizQuestions')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Quiz $quiz = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getQuestion(): ?string
    {
        return $this->question;
    }

    public function setQuestion(string $question): static
    {
        $this->question = $question;

        return $this;
    }

    public function getPossibleAnswers(): array
    {
        return $this->possibleAnswers;
    }

    public function setPossibleAnswers(array $possibleAnswers): static
    {
        $this->possibleAnswers = $possibleAnswers;

        return $this;
    }

    public function getCorrectAnswerIndex(): ?int
    {
        return $this->correctAnswerIndex;
    }

    public function setCorrectAnswerIndex(int $correctAnswerIndex): static
    {
        $this->correctAnswerIndex = $correctAnswerIndex;

        return $this;
    }

    public function getQuiz(): ?Quiz
    {
        return $this->quiz;
    }

    public function setQuiz(?Quiz $quiz): static
    {
        $this->quiz = $quiz;

        return $this;
    }

    public function getOwner(): ?UserInterface
    {
        return $this->getQuiz()->getTeacher();
    }
}
