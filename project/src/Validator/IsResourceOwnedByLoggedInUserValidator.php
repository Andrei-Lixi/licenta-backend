<?php

namespace App\Validator;

use App\Interface\OwnedResourceInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

class IsResourceOwnedByLoggedInUserValidator extends ConstraintValidator
{

    public function __construct(
        private readonly TokenStorageInterface $tokenStorage
    )
    {
    }

    public function validate(mixed $value, Constraint $constraint)
    {
        if (!$constraint instanceof IsResourceOwnedByLoggedInUser) {
            throw new UnexpectedTypeException($constraint, IsResourceOwnedByLoggedInUser::class);
        }

        if (!$value instanceof OwnedResourceInterface) {
            throw new UnexpectedTypeException($value, OwnedResourceInterface::class);
        }

        $loggedInUser = $this->tokenStorage->getToken()->getUser();
        if ($loggedInUser !== $value->getOwner()) {
            $this->context->buildViolation('Associated resource is not owned by logged in user')->addViolation();
        }
    }
}