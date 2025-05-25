<?php

namespace App\Entity;

use App\Entity\UserExtension\AdminUser;
use App\Entity\UserExtension\StudentUser;
use App\Entity\UserExtension\TeacherUser;
use App\Enum\UserEnum;
use App\Repository\UserRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation\Timestampable;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Serializer\Attribute\Groups;


#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\InheritanceType('JOINED')]
#[ORM\DiscriminatorColumn(name: 'discriminator', type: 'string')]
#[ORM\DiscriminatorMap([
    UserEnum::ADMIN->value => AdminUser::class,
    UserEnum::STUDENT->value => StudentUser::class,
    UserEnum::TEACHER->value => TeacherUser::class
])]
abstract class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[Groups(['user', 'course'])]
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[Groups('user')]
    #[ORM\Column(length: 255)]
    private ?string $email = null;

    #[ORM\Column(length: 255)]
    private ?string $password = null;


    #[Timestampable(on: 'create')]
    #[ORM\Column]
    private ?\DateTimeImmutable $createdAt = null;


    #[Timestampable(on: 'update')]
    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $updatedAt = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): static
    {
        $this->email = $email;

        return $this;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): static
    {
        $this->password = $password;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeImmutable $createdAt): static
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getUpdatedAt(): ?\DateTimeInterface
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(?\DateTimeInterface $updatedAt): static
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    public function getUserIdentifier(): string
    {
        return $this->id;
    }

    public function eraseCredentials(): void
    {

    }
}
