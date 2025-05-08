<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Post;
use App\Controller\RefreshTokenController;
use Doctrine\ORM\Mapping as ORM;
use Gesdinet\JWTRefreshTokenBundle\Entity\RefreshToken as BaseRefreshToken;

#[ApiResource(
    operations: [
        new Post(
            uriTemplate: '/auth/token/refresh',
            controller: RefreshTokenController::class,
        ),
    ],
)]
#[ORM\Entity]
#[ORM\Table(name: 'refresh_tokens')]
class RefreshToken extends BaseRefreshToken
{
}
