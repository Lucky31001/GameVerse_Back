<?php

namespace App\Controller;

use Gesdinet\JWTRefreshTokenBundle\Generator\RefreshTokenGeneratorInterface;
use Gesdinet\JWTRefreshTokenBundle\Model\RefreshTokenManagerInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\Security\Core\User\UserProviderInterface;

#[AsController]
class RefreshTokenController
{
    public function __construct(
        private RefreshTokenManagerInterface $refreshTokenManager,
        private RefreshTokenGeneratorInterface $refreshTokenGenerator,
        private JWTTokenManagerInterface     $tokenGenerator,
        private UserProviderInterface        $userProvider
    ) {
    }

    public function __invoke(Request $request): JsonResponse
    {
        $content = json_decode($request->getContent(), true);
        $refreshToken = $content['refresh_token'] ?? null;

        if (!$refreshToken) {
            return new JsonResponse(['error' => 'No refresh token provided.'], 400);
        }

        $tokenEntity = $this->refreshTokenManager->get($refreshToken);

        if (!$tokenEntity || !$tokenEntity->isValid()) {
            return new JsonResponse(['error' => 'Invalid or expired refresh token.'], 401);
        }

        $user = $tokenEntity->getUsername();
        if (is_string($user)) {
            $user = $this->userProvider->loadUserByIdentifier($user);
        }

        $this->refreshTokenManager->delete($tokenEntity);
        $newRefreshToken = $this->refreshTokenGenerator->createForUserWithTtl($user, 2592000);
        $this->refreshTokenManager->save($newRefreshToken);

        return new JsonResponse([
            'token' => $this->tokenGenerator->create($user),
            'refresh_token' => $newRefreshToken->getRefreshToken(),
        ]);
    }
}
