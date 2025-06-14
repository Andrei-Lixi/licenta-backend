<?php

namespace App\Model;

use App\Entity\User;
use App\Entity\UserExtension\StudentUser;
use App\Entity\UserExtension\TeacherUser;
use App\Enum\UserEnum;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\IsTrue;
use Symfony\Component\Validator\Constraints\NotNull;
use Symfony\Component\Validator\Constraints\Type;

class NewUserModel
{
    #[NotNull]
    #[Type('string')]
    #[Email]
    public $email = null;

    #[NotNull]
    #[Type('string')]
    public $password = null;

    #[NotNull]
    #[Type('string')]
    public $type = null;

    #[NotNull]
    #[Type('string')]
    public $name = null;

    #[NotNull]
    #[Type('string')]
    public $school = null;

    #[IsTrue]
    public function isAllowedType(): bool
    {
        return in_array($this->type, [UserEnum::STUDENT->value, UserEnum::TEACHER->value]);
    }

    public function toUserEntity(UserPasswordHasherInterface $userPasswordHasher): User
    {
        $user = match ($this->type) {
            UserEnum::TEACHER->value => new TeacherUser(),
            UserEnum::STUDENT->value => new StudentUser()
        };

        $user->setEmail($this->email);
        $user->setName($this->name);
        $user->setSchool($this->school);
        $hashedPassword = $userPasswordHasher->hashPassword($user, $this->password);
        $user->setPassword($hashedPassword);

        return $user;
    }
}