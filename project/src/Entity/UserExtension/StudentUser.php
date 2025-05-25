<?php

namespace App\Entity\UserExtension;

use App\Entity\User;
use App\Repository\StudentUserRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: StudentUserRepository::class)]
class StudentUser extends User
{
    public function getRoles(): array
    {
        return ['ROLE_USER'];
    }
}
