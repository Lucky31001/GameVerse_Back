<?php

// src/Controller/LogoutController.php

namespace App\Controller;

use Gesdinet\JWTRefreshTokenBundle\Model\RefreshTokenManagerInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class LogoutController extends AbstractController
{
    public function __construct(
        private RefreshTokenManagerInterface $refreshTokenManager,
    ) {
    }

    public function __invoke(Security $security): JsonResponse
    {

        $user = $security->getUser();

        if (!$user) {
            return new JsonResponse(['message' => 'Already logged out'], 200);
        }

        $refreshToken = $this->refreshTokenManager->getLastFromUsername($user->getUserIdentifier());

        if ($refreshToken) {
            $this->refreshTokenManager->delete($refreshToken);
        }

        return new JsonResponse(['message' => 'Successfully logged out']);
    }
}
