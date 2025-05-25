<?php

namespace App\DataFixtures;

use App\Constant\FixtureData;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{
    public function __construct(
        private readonly UserPasswordHasherInterface $passwordHasher
    )
    {
    }

    public function load(ObjectManager $manager): void
    {
        foreach (FixtureData::USER_DATA as $userData) {
            $user = new $userData['class'];
            $user->setEmail($userData['email']);
            $hashedPassword = $this->passwordHasher->hashPassword($user, $userData['password']);
            $user->setPassword($hashedPassword);

            $manager->persist($user);
        }

        $manager->flush();
    }
}
