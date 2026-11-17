<?php

namespace App\Tests\Functional;

use App\Application\Interfaces\RefreshTokenRepositoryInterface;
use App\Application\Interfaces\UserRepositoryInterface;
use App\Tests\Fixtures\RefreshTokenFixture;
use App\Tests\Fixtures\UserFixture;
use App\Infrastructure\Security\RefreshToken\Sha256RefreshTokenHasher;
use App\Tests\Fakers\InMemoryRefreshTokenRepository;
use App\Tests\Fakers\InMemoryUserRepository;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Clock\ClockInterface;
use Symfony\Component\Clock\MockClock;

class AuthenticationRefreshTokenFunctionalTest extends WebTestCase
{
    public function testItAuthenticatesUser(): void
    {
        $rawRefreshToken = 'refresh-token';
        $tokenHasher = new Sha256RefreshTokenHasher();
        $tokenHash = $tokenHasher->hash($rawRefreshToken);

        $userStub = UserFixture::create();

        $stubRefreshToken = RefreshTokenFixture::create(
            tokenHash: $tokenHash,
        );

        $userRepo = new InMemoryUserRepository($userStub);
        $tokenRepo = new InMemoryRefreshTokenRepository($stubRefreshToken);

        $client = self::createClient();

        self::getContainer()->set(
            ClockInterface::class,
            new MockClock('2026-10-01 12:00:00'),
        );

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
