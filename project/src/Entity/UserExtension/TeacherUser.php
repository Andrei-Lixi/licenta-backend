<?php

namespace App\Entity\UserExtension;

use App\Entity\Course;
use App\Entity\Lesson;
use App\Entity\Quiz;
use App\Entity\User;
use App\Repository\TeacherUserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: TeacherUserRepository::class)]
class TeacherUser extends User
{
    #[ORM\Column]
    private bool $active = false;

    public function isActive(): bool
    {
        return $this->active;
    }


    /**
     * @var Collection<int, Quiz>
     */
    #[ORM\OneToMany(targetEntity: Quiz::class, mappedBy: 'teacher', orphanRemoval: true)]
    private Collection $quizzes;



    public function setActive(bool $active): void
    {
        $this->active = $active;
    }

    /**
     * @var Collection<int, Lesson>
     */
    #[ORM\OneToMany(targetEntity: Lesson::class, mappedBy: 'teacherUser', orphanRemoval: true)]
    private Collection $lessons;

    public function __construct()
    {
        $this->quizzes = new ArrayCollection();
        $this->lessons = new ArrayCollection();
    }

    public function getRoles(): array
    {
        return ['ROLE_TEACHER', 'ROLE_USER'];
    }



    /**
     * @return Collection<int, Quiz>
     */
    public function getQuizzes(): Collection
    {
        return $this->quizzes;
    }

    public function addQuiz(Quiz $quiz): static
    {
        if (!$this->quizzes->contains($quiz)) {
            $this->quizzes->add($quiz);
            $quiz->setTeacher($this);
        }

        return $this;
    }

    public function removeQuiz(Quiz $quiz): static
    {
        if ($this->quizzes->removeElement($quiz)) {
            // set the owning side to null (unless already changed)
            if ($quiz->getTeacher() === $this) {
                $quiz->setTeacher(null);
            }
        }

        return $this;
    }

    
    /**
     * @return Collection<int, Lesson>
     */
    public function getLessons(): Collection
    {
        return $this->lessons;
    }

    public function addLesson(Lesson $lesson): static
    {
        if (!$this->lessons->contains($lesson)) {
            $this->lessons->add($lesson);
            $lesson->setTeacherUser($this);
        }

        return $this;
    }

    public function removeLesson(Lesson $lesson): static
    {
        if ($this->lessons->removeElement($lesson)) {
            // set the owning side to null (unless already changed)
            if ($lesson->getTeacherUser() === $this) {
                $lesson->setTeacherUser(null);
            }
        }

        return $this;
    }
}
