<?php
// src/Entity/QuizAttempt.php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;
use App\Repository\QuizAttemptRepository;

#[ORM\Entity(repositoryClass: QuizAttemptRepository::class)]



class QuizAttempt
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: Quiz::class)]
    #[ORM\JoinColumn(nullable: false)]
    private ?Quiz $quiz = null;

    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(nullable: false)]
    private ?UserInterface $user = null;

    #[ORM\Column(type: 'integer')]
    private int $correctAnswersCount = 0;

    #[ORM\Column(type: 'integer')]
    private int $totalQuestions = 0;

    #[ORM\Column(type: 'datetime')]
    private \DateTimeInterface $attemptedAt;

    public function __construct()
    {
        $this->attemptedAt = new \DateTime();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getQuiz(): ?Quiz
    {
        return $this->quiz;
    }

    public function setQuiz(?Quiz $quiz): self
    {
        $this->quiz = $quiz;
        return $this;
    }

    public function getUser(): ?UserInterface
    {
        return $this->user;
    }

    public function setUser(?UserInterface $user): self
    {
        $this->user = $user;
        return $this;
    }

    public function getCorrectAnswersCount(): int
    {
        return $this->correctAnswersCount;
    }

    public function setCorrectAnswersCount(int $correctAnswersCount): self
    {
        $this->correctAnswersCount = $correctAnswersCount;
        return $this;
    }

    public function getTotalQuestions(): int
    {
        return $this->totalQuestions;
    }

    public function setTotalQuestions(int $totalQuestions): self
    {
        $this->totalQuestions = $totalQuestions;
        return $this;
    }

    public function getAttemptedAt(): \DateTimeInterface
    {
        return $this->attemptedAt;
    }

    public function setAttemptedAt(\DateTimeInterface $attemptedAt): self
    {
        $this->attemptedAt = $attemptedAt;
        return $this;
    }
}
