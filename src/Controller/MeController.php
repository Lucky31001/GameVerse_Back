<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;

class MeController extends AbstractController
{
    public function __invoke(Security $security)
    {
        $user = $security->getUser();

        if ($user === null) {
            return $this->json(['error' => 'User not found.'], 404);
        }

        return $user;
    }
}
