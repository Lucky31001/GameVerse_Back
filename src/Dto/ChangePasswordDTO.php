<?php

namespace App\Dto;

use Symfony\Component\Validator\Constraints as Assert;

class ChangePasswordDTO
{
    #[Assert\NotBlank]
    #[Assert\Length(min: 8, minMessage: "le mot de passe doit contenir au moins {{ limit }} caractères")]
    #[Assert\Regex(
        pattern: '/[A-Z]/',
        message: 'le mot de passe doit contenir au moins une lettre majuscule'
    )]
    #[Assert\Regex(
        pattern: '/[a-z]/',
        message: 'le mot de passe doit contenir au moins une lettre minuscule'
    )]
    #[Assert\Regex(
        pattern: '/[0-9]/',
        message: 'le mot de passe doit contenir au moins un chiffre'
    )]
    #[Assert\Regex(
        pattern: '/[\W_]/',
        message: 'le mot de passe doit contenir au moins un caractère spécial'
    )]
    #[Assert\Regex(
        pattern: '/^(?!.*\s).*$/',
        message: 'le mot de passe ne doit pas contenir d\'espaces'
    )]
    private string $oldPassword;
    #[Assert\NotBlank]
    #[Assert\Length(min: 8, minMessage: "le mot de passe doit contenir au moins {{ limit }} caractères")]
    #[Assert\Regex(
        pattern: '/[A-Z]/',
        message: 'le mot de passe doit contenir au moins une lettre majuscule'
    )]
    #[Assert\Regex(
        pattern: '/[a-z]/',
        message: 'le mot de passe doit contenir au moins une lettre minuscule'
    )]
    #[Assert\Regex(
        pattern: '/[0-9]/',
        message: 'le mot de passe doit contenir au moins un chiffre'
    )]
    #[Assert\Regex(
        pattern: '/[\W_]/',
        message: 'le mot de passe doit contenir au moins un caractère spécial'
    )]
    #[Assert\Regex(
        pattern: '/^(?!.*\s).*$/',
        message: 'le mot de passe ne doit pas contenir d\'espaces'
    )]
    private string $newPassword;

    public function getOldPassword(): string
    {
        return $this->oldPassword;
    }

    public function setOldPassword(string $oldPassword): void
    {
        $this->oldPassword = $oldPassword;
    }

    public function getNewPassword(): string
    {
        return $this->newPassword;
    }

    public function setNewPassword(string $newPassword): void
    {
        $this->newPassword = $newPassword;
    }


}
