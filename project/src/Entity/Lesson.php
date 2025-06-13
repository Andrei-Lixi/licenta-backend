<?php

namespace App\Entity;

use App\Entity\UserExtension\TeacherUser;
use App\Interface\OwnedResourceInterface;
use App\Repository\LessonRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Serializer\Attribute\Groups;

#[ORM\Entity(repositoryClass: LessonRepository::class)]
class Lesson implements OwnedResourceInterface
{
    #[Groups(['lesson', 'lesson_id'])]
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[Groups(['lesson'])]
    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\ManyToOne(inversedBy: 'lessons')]
    #[ORM\JoinColumn(nullable: false)]
    private ?TeacherUser $teacherUser = null;


     #[Groups(['lesson'])]
    #[ORM\Column(length: 255)]
    private ?string $field = null;

    #[Groups(['lesson'])]
    #[ORM\Column(length: 255)]
    private ?string $grade = null;

    #[Groups(['lesson'])]
    #[ORM\Column(length: 255, unique: true, nullable: true)]
    private ?string $filename = null;


    public function getFilename(): ?string
    {
        return $this->filename;
    }

    public function setFilename(?string $filename): void
    {
        $this->filename = $filename;
    }

    public function setGrade(?string $grade): void
    {
        $this->grade = $grade;
    }

    public function getGrade(): ?string
    {
        return $this->grade;
    }

    public function setField(?string $field): void
    {
        $this->field = $field;
    }

    public function getField(): ?string
    {
        return $this->field;
    }



    
    public function getId(): ?int
    {
        return $this->id;
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

    public function getTeacherUser(): ?TeacherUser
    {
        return $this->teacherUser;
    }

    public function setTeacherUser(?TeacherUser $teacherUser): static
    {
        $this->teacherUser = $teacherUser;

        return $this;
    }

    public function getOwner(): ?UserInterface
    {
        return $this->getTeacherUser();
    }
}
