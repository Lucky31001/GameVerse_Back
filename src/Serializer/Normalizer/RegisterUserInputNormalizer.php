<?php

namespace App\Serializer\Normalizer;

use App\Dto\RegisterUserDTO;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

class RegisterUserInputNormalizer implements NormalizerInterface
{
    public function normalize($object, ?string $format = null, array $context = []): array
    {
        return [
            'username' => $object->getUsername(),
            'email' => $object->getEmail(),
        ];
    }

    public function supportsNormalization($data, ?string $format = null, array $context = []): bool
    {
        return $data instanceof RegisterUserDTO;
    }

    public function getSupportedTypes(?string $format): array
    {
        return [RegisterUserDTO::class => true];
    }
}
