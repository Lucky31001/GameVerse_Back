<?php

// src/State/RegisterUserProcessor.php

namespace App\Processor;

use App\Dto\RegisterUserDTO;
use App\Entity\User;
use App\Repository\UserRepository;
use Symfony\Component\PasswordHasher\PasswordHasherInterface;

class RegisterUserProcessor
{
    public function __construct(
        private UserRepository $userRepository,
        private PasswordHasherInterface $passwordHasher
    ) {
    }

    public function __invoke(RegisterUserDTO $data): User
    {
        $user = new User();
        $user->setUsername($data->getUsername());
        $user->setEmail($data->getEmail());

        $encodedPassword = $this->passwordHasher->hash($data->getPassword());
        $user->setPassword($encodedPassword);

        $this->userRepository->save($user);

        return $user;
    }
}
