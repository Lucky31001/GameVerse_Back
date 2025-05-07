<?php

namespace App\Tests;

use ApiPlatform\Symfony\Bundle\Test\ApiTestCase;
use App\Repository\UserRepository;

class LoginTest extends ApiTestCase
{
    private UserRepository $userRepository;

    protected function setUp(): void
    {
        parent::setUp();
        self::bootKernel();
        $container = static::getContainer();
        $this->userRepository = $container->get(UserRepository::class);
    }

    public function testLoginSuccess(): void
    {
        $client = static::createClient();

        $client->request('POST', '/auth/login', [
            'json' => [
                'email' => 'test@test.com',
                'password' => 'Azerty123.'
            ]
        ]);

        $this->assertResponseIsSuccessful();
        $data = json_decode($client->getResponse()->getContent(), true);
        $this->assertArrayHasKey('token', $data);
    }

    public function testLoginFailure(): void
    {
        $client = static::createClient();

        $client->request('POST', '/auth/login', [
            'json' => [
                'email' => 'test@test.com',
                'password' => 'WrongPassword'
            ]
        ]);

        $this->assertResponseStatusCodeSame(400);
    }
}
