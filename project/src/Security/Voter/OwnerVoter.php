<?php

namespace App\Security\Voter;

use App\Interface\OwnedResourceInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class OwnerVoter extends Voter
{
    const IS_RESOURCE_OWNER = 'IS_RESOURCE_OWNER';

    protected function supports(string $attribute, mixed $subject): bool
    {
        if ($attribute !== self::IS_RESOURCE_OWNER) {
            return false;
        }

        if (!$subject instanceof OwnedResourceInterface) {
            return false;
        }

        return true;
    }

    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();
        if (empty($user)) {
            return false;
        }

        /* @var OwnedResourceInterface $subject**/
        $owner = $subject->getOwner();

        if ($user !== $owner) {
            return false;
        }

        return true;
    }
}