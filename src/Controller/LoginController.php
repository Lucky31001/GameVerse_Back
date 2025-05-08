<?php

// src/Controller/LoginController.php

namespace App\Controller;

use App\Dto\LoginUserDTO;
use App\Entity\RefreshToken;
use Gesdinet\JWTRefreshTokenBundle\Generator\RefreshTokenGeneratorInterface;
use Gesdinet\JWTRefreshTokenBundle\Model\RefreshTokenManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[AsController]
class LoginController extends AbstractController
{
    public function __construct(
        private Security $security,
        private UserPasswordHasherInterface $passwordHasher,
        private UserProviderInterface $userProvider,
        private ValidatorInterface $validator,
        private JWTTokenManagerInterface $tokenGenerator,
        private RefreshTokenManagerInterface $refreshTokenManager,
        private RefreshTokenGeneratorInterface $refreshTokenGenerator,
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
        $lastRefreshToken = $this->refreshTokenManager->getLastFromUsername($user->getUserIdentifier());
        if ($lastRefreshToken) {
            $this->refreshTokenManager->delete($lastRefreshToken);
        }

        $refreshToken = $this->refreshTokenGenerator->createForUserWithTtl($user, 2592000);

        $this->refreshTokenManager->save($refreshToken);
        $token = $this->tokenGenerator->create($user);

        return new JsonResponse(['token' => $token, 'refresh_token' => $refreshToken->getRefreshToken()], 200);
    }
}
