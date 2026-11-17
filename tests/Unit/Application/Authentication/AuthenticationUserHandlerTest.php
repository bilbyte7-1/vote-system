<?php

namespace App\Tests\Unit\Application\Authentication;

use App\Application\Authentication\AuthenticationUserHandler;
use App\Application\Authentication\AuthenticationUserRequest;
use App\Application\Interfaces\AccessTokenIssuerInterface;
use App\Application\Interfaces\AuthenticationProviderInterface;
use App\Application\Interfaces\RefreshTokenIssuerInterface;
use App\Application\Interfaces\RefreshTokenRepositoryInterface;
use App\Application\RefreshToken\RefreshTokenInitializer;
use App\Tests\Fixtures\RefreshTokenFixture;
use App\Tests\Fixtures\UserFixture;
use PHPUnit\Framework\TestCase;

class AuthenticationUserHandlerTest extends TestCase
{
    public function testItAuthenticationUser(): void
    {
        $user = UserFixture::create();

        $refreshToken = RefreshTokenFixture::create(
            userId: $user->getId()->getId(),
        );

        $request = new AuthenticationUserRequest(
            email: 'user@example.com',
            plainPassword: 'password123',
        );

        $mockAuthenticationProvider = $this->createMock(AuthenticationProviderInterface::class);
        $mockAuthenticationProvider
            ->expects($this->once())
            ->method('authenticate')
            ->with(
                $request->email,
                $request->plainPassword,
            )
            ->willReturn($user);

        $stubAccessTokenIssuer = $this->createStub(AccessTokenIssuerInterface::class);
        $stubAccessTokenIssuer
            ->method('issue')
            ->willReturn('fake-access-token');
        $stubRefreshTokenIssuer = $this->createStub(RefreshTokenIssuerInterface::class);
        $stubRefreshTokenIssuer
            ->method('issue')
            ->willReturn('refresh-token');

        $mockRefreshTokenInitializer = $this->createMock(RefreshTokenInitializer::class);
        $mockRefreshTokenInitializer
            ->expects(self::once())
            ->method('initialize')
            ->with(
                $user->getId(),
                'refresh-token'
            )
            ->willReturn($refreshToken);
        $mockRefreshTokenPersister = $this->createMock(RefreshTokenRepositoryInterface::class);
        $mockRefreshTokenPersister
            ->expects(self::once())
            ->method('save')
            ->with(self::identicalTo($refreshToken));

        $handle = new AuthenticationUserHandler(
            $mockAuthenticationProvider,
            $stubAccessTokenIssuer,
            $stubRefreshTokenIssuer,
            $mockRefreshTokenInitializer,
            $mockRefreshTokenPersister,
        );

        $authResult = $handle->handle($request);

        self::assertSame(
            'fake-access-token',
            $authResult->accessToken
        );

        self::assertSame(
            'refresh-token',
            $authResult->refreshToken
        );
    }
}
