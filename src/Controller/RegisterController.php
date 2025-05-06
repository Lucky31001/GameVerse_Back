<?php

// src/Controller/RegisterController.php

namespace App\Controller;

use App\Dto\RegisterUserDTO;
use App\Entity\User;
use App\Repository\UserRepository;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class RegisterController extends AbstractController
{
    public function __construct(
        private UserPasswordHasherInterface $passwordHasher,
        private UserRepository $userRepository,
        private JWTTokenManagerInterface $tokenGenerator,
        private ValidatorInterface $validator
    ) {
    }

    public function __invoke(RegisterUserDTO $dto): JsonResponse
    {
        $violations = $this->validator->validate($dto);
        if (count($violations) > 0) {
            $errors = [];
            foreach ($violations as $violation) {
                $errors[] = $violation->getMessage();
            }
            return new JsonResponse(['errors' => $errors], 400);
        }

        // Vérifier si l'email ou le nom d'utilisateur existe déjà
        if ($this->userRepository->findOneBy(['email' => $dto->getEmail()])) {
            return new JsonResponse(['error' => 'Email already used'], 400);
        }

        if ($this->userRepository->findOneBy(['username' => $dto->getUsername()])) {
            return new JsonResponse(['error' => 'Username already taken'], 400);
        }

        $user = new User();
        $user->setUsername($dto->getUsername());
        $user->setEmail($dto->getEmail());

        $user->setPassword($this->passwordHasher->hashPassword($user, $dto->getPassword()));

        $token = $this->tokenGenerator->create($user);

        $this->userRepository->save($user);


        return new JsonResponse([
            'username' => $user->getUsername(),
            'token' => $token
        ]);
    }
}
