<?php

namespace App\Dto;

use Symfony\Component\Validator\Constraints as Assert;

class RegisterUserDTO
{
    #[Assert\NotBlank]
    #[Assert\Length(min: 3, max: 50)]
    private string $username;

    #[Assert\NotBlank]
    #[Assert\Email]
    private string $email;

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
    private string $password;

    // Getters et setters
    public function getUsername(): string
    {
        return $this->username;
    }

    public function setUsername(string $username): void
    {
        $this->username = $username;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function setEmail(string $email): void
    {
        $this->email = $email;
    }

    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): void
    {
        $this->password = $password;
    }
}
