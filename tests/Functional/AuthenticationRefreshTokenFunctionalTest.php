<?php

namespace App\Tests\Functional;

use App\Application\Interfaces\RefreshTokenRepositoryInterface;
use App\Application\Interfaces\UserRepositoryInterface;
use App\Domain\RefreshToken\RefreshToken;
use App\Domain\RefreshToken\RefreshTokenHash;
use App\Domain\RefreshToken\RefreshTokenId;
use App\Domain\User\Email;
use App\Domain\User\Password;
use App\Domain\User\User;
use App\Domain\User\UserId;
use App\Infrastructure\Security\RefreshToken\Sha256RefreshTokenHasher;
use App\Tests\Fakers\InMemoryRefreshTokenRepository;
use App\Tests\Fakers\InMemoryUserRepository;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

class AuthenticationRefreshTokenFunctionalTest extends WebTestCase
{
    public function testItAuthenticatesUser(): void
    {
        $rawRefreshToken = 'refresh-token';
        $tokenHasher = new Sha256RefreshTokenHasher();
        $tokenHash = $tokenHasher->hash($rawRefreshToken);

        $userStub = new User (
            new UserId('user-id'),
            new Email('1@example.com'),
            new Password('password')
        );

        $stubRefreshToken = new RefreshToken(
            new RefreshTokenId('refresh-token-id'),
            new UserId('user-id'),
            new RefreshTokenHash($tokenHash),
            new \DateTimeImmutable('2026-09-17 12:00:00'),
            new \DateTimeImmutable('2026-10-17 12:00:00'),
            null
        );

        $userRepo = new InMemoryUserRepository($userStub);
        $tokenRepo = new InMemoryRefreshTokenRepository($stubRefreshToken);

        $client = self::createClient();

        self::getContainer()->set(
            UserRepositoryInterface::class,
            $userRepo,
        );

        self::getContainer()->set(
            RefreshTokenRepositoryInterface::class,
            $tokenRepo,
        );
        $client->jsonRequest(
            method: 'POST',
            uri: '/users/refresh',
            parameters: [
                'refresh_token' => $rawRefreshToken,
            ],
        );

        self::assertResponseStatusCodeSame(
            Response::HTTP_OK,
        );

        $content = $client
            ->getResponse()
            ->getContent();

        $response = json_decode(
            $content,
            true,
            flags: JSON_THROW_ON_ERROR,
        );

        self::assertArrayHasKey('access_token', $response);
        self::assertNotEmpty($response['access_token']);
        self::assertArrayHasKey('refresh_token', $response);
        self::assertNotEmpty($response['refresh_token']);
    }
}
