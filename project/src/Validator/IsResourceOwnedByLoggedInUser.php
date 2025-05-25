<?php

namespace App\Validator;

use Symfony\Component\Validator\Constraint;
#[\Attribute]
class IsResourceOwnedByLoggedInUser extends Constraint
{
    public function getTargets(): string|array
    {
        return self::PROPERTY_CONSTRAINT;
    }
}