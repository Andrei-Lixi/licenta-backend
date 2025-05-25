<?php

namespace App\Interface;

use Symfony\Component\Security\Core\User\UserInterface;

interface OwnedResourceInterface
{
    public function getOwner(): ?UserInterface;
}