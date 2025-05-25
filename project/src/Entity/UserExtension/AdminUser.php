<?php

namespace App\Entity\UserExtension;

use App\Entity\User;
use App\Repository\AdminUserRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: AdminUserRepository::class)]
class AdminUser extends User
{

    public function getRoles(): array
    {
        return ['ROLE_ADMIN', 'ROLE_USER'];
    }
}
