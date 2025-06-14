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


    #[Groups(['lesson'])]
    #[ORM\ManyToOne(inversedBy: 'lessons')]
    #[ORM\JoinColumn(nullable: false)]
    private ?TeacherUser $teacherUser = null;


        #[ORM\Column(type: 'integer')]
        private $views = 0;

        #[ORM\Column(type: 'integer')]
        private $likes = 0;

        #[ORM\Column(type: 'integer')]
        private $dislikes = 0;


        


    #[Groups(['lesson'])]
    public function getTeacherUserName(): ?string
    {
        return $this->teacherUser ? $this->teacherUser->getName() : null;
    }

     #[Groups(['lesson'])]
    #[ORM\Column(length: 255)]
    private ?string $field = null;

    #[Groups(['lesson'])]
    #[ORM\Column(length: 255)]
    private ?string $grade = null;

    #[Groups(['lesson'])]
    #[ORM\Column(length: 255, unique: true, nullable: true)]
    private ?string $filename = null;



public function getViews(): int
{
    return $this->views;
}

public function setViews(int $views): self
{
    $this->views = $views;
    return $this;
}



public function getLikes(): int
{
    return $this->likes;
}

public function setLikes(int $likes): self
{
    $this->likes = $likes;
    return $this;
}

public function getDislikes(): int
{
    return $this->dislikes;
}

public function setDislikes(int $dislikes): self
{
    $this->dislikes = $dislikes;
    return $this;
}

    
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
