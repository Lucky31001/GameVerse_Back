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
        $user->setPassword($this->passwordHasher->hashPassword("Azerty123.")); // Assure-toi d'utiliser un hasher pour le mot de passe
        $user->setRoles(['ROLE_USER']);

        $this->userRepository->save($user);

        $this->addReference('user_test', $user);
    }
}
