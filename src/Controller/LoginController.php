<?php

// src/Controller/LoginController.php

namespace App\Controller;

use App\Dto\LoginUserDTO;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use App\Entity\User;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[AsController]

class LoginController extends AbstractController
{
    public function __construct(
        private UserPasswordHasherInterface $passwordHasher,
        private UserProviderInterface $userProvider,
        private ValidatorInterface $validator,
        private JWTTokenManagerInterface $tokenGenerator,
    ) {

    }
    public function __invoke(LoginUserDTO $dto): JsonResponse
    {

        $violations = $this->validator->validate($dto);
        if (count($violations) > 0) {
            $errors = [];
            foreach ($violations as $violation) {
                $errors[] = $violation->getMessage();
            }
            return new JsonResponse(['errors' => $errors], 400);
        }

        if ($dto->getEmail() == null || $dto->getPassword() == null) {
            return new JsonResponse(['error' => 'Email and password required.'], 400);
        }

        try {
            $user = $this->userProvider->loadUserByIdentifier($dto->getEmail());
        } catch (\Exception $e) {
            return new JsonResponse(['error' => 'User not found.'], 404);
        }

        if (!$this->passwordHasher->isPasswordValid($user, $dto->getPassword())) {
            return new JsonResponse(['error' => 'Invalid credentials.'], 401);
        }

        $token = $this->tokenGenerator->create($user);

        return new JsonResponse(['token' => $token]);
    }
}
