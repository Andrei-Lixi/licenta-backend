<?php

namespace App\Validator;

use Symfony\Component\Validator\Constraint;

#[\Attribute]
class EntityExists extends Constraint
{

    public string $entityClass;
    public string $message;

    public function __construct(string $entityClass, string $message, ?array $groups = null, $payload = null)
    {
        parent::__construct([], $groups, $payload);
        $this->entityClass = $entityClass;
        $this->message = $message;
    }

    public function getTargets(): string|array
    {
        return self::PROPERTY_CONSTRAINT;
    }

}