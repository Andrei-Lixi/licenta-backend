<?php

namespace App\Entity;

use App\Entity\UserExtension\TeacherUser;
use App\Interface\OwnedResourceInterface;
use App\Repository\QuizRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Serializer\Attribute\Groups;

#[ORM\Entity(repositoryClass: QuizRepository::class)]
class Quiz implements OwnedResourceInterface
{
    #[Groups('quiz')]
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;


    /**
     * @var Collection<int, QuizQuestion>
     */
    #[Groups('quiz')]
    #[ORM\OneToMany(targetEntity: QuizQuestion::class, mappedBy: 'quiz', orphanRemoval: true)]
    private Collection $quizQuestions;

    #[ORM\ManyToOne(inversedBy: 'quizzes')]
    #[ORM\JoinColumn(nullable: false)]
    private ?TeacherUser $teacher = null;

    #[Groups('quiz')]
    #[ORM\Column(length: 255)]
    private ?string $name = null;

    public function __construct()
    {
        $this->quizQuestions = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return Collection<int, QuizQuestion>
     */
    public function getQuizQuestions(): Collection
    {
        return $this->quizQuestions;
    }

    public function addQuizQuestion(QuizQuestion $quizQuestion): static
    {
        if (!$this->quizQuestions->contains($quizQuestion)) {
            $this->quizQuestions->add($quizQuestion);
            $quizQuestion->setQuiz($this);
        }

        return $this;
    }

    public function removeQuizQuestion(QuizQuestion $quizQuestion): static
    {
        if ($this->quizQuestions->removeElement($quizQuestion)) {
            // set the owning side to null (unless already changed)
            if ($quizQuestion->getQuiz() === $this) {
                $quizQuestion->setQuiz(null);
            }
        }

        return $this;
    }

    public function getTeacher(): ?TeacherUser
    {
        return $this->teacher;
    }

    public function setTeacher(?TeacherUser $teacher): static
    {
        $this->teacher = $teacher;

        return $this;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getOwner(): ?UserInterface
    {
        return $this->getTeacher();
    }
}
