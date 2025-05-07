<?php

namespace App\DataFixtures;

use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserFixtures extends Fixture
{
    public function __construct(
        private UserPasswordHasherInterface $passwordHasher,
        private UserRepository $userRepository
    ) {

    }
    public function load(ObjectManager $manager): void
    {
        $user = new User();
        $user->setEmail('test@test.com');
        $user->setUsername('test');
        $user->setRoles(['ROLE_USER']);
        $user->setPassword($this->passwordHasher->hashPassword($user, "Azerty123."));
        $this->userRepository->save($user);
        $this->addReference('user_test', $user);
    }
}
