<?php

namespace App\Validator;

use App\Entity\UserExtension\TeacherUser;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

class EntityExistsValidator extends ConstraintValidator
{

    public function __construct(
        private readonly EntityManagerInterface $entityManager
    )
    {
    }

    public function validate(mixed $value, Constraint $constraint)
    {
        if (!$constraint instanceof EntityExists) {
            throw new UnexpectedTypeException($constraint, EntityExists::class);
        }

        if (empty($value) || !is_numeric($value)) {
            return;
        }

        $teacherUser = $this->entityManager->getRepository($constraint->entityClass)->find($value);
        if (empty($teacherUser)) {
            $this->context->buildViolation($constraint->message)->addViolation();
        }
    }
}