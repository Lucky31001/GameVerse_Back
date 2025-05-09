<?php

namespace App\Controller;

use App\Dto\ChangePasswordDTO;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[AsController]
class ChangePasswordController extends AbstractController
{
    public function __construct(
        private UserPasswordHasherInterface $passwordHasher,
        private ValidatorInterface $validator,
        private Security $security,
        private UserProviderInterface $userProvider,
        private UserRepository $userRepository,
    ) {

    }

    public function __invoke(ChangePasswordDTO $dto): JsonResponse
    {
        $user = $this->security->getUser();

        if (!$user) {
            return new JsonResponse(['error' => 'You must be logged in'], 404);
        }

        $violations = $this->validator->validate($dto);
        if (count($violations) > 0) {
            $errors = [];
            foreach ($violations as $violation) {
                $errors[] = $violation->getMessage();
            }
            return new JsonResponse(['errors' => $errors], 400);
        }

        if (!$this->passwordHasher->isPasswordValid($user, $dto->getOldPassword())) {
            return new JsonResponse(['error' => 'Invalid old password'], 400);
        }

        if ($dto->getNewPassword() === $dto->getOldPassword()) {
            return new JsonResponse(['error' => 'New password must be different from old password'], 400);
        }

        $user->setPassword($this->passwordHasher->hashPassword($user, $dto->getNewPassword()));

        $this->userRepository->save($user);

        return new JsonResponse(['message' => 'Password changed successfully'], 200);
    }
}
