<?php

namespace App\Tests\Unit\Infrastructure;

use App\Domain\User\Email;
use App\Domain\User\Password;
use App\Domain\User\User;
use App\Domain\User\UserId;
use App\Infrastructure\Security\LexikAccessTokenIssuer;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Security\Core\User\UserInterface;

class LexikAccessTokenIssueTest extends TestCase
{
    public function testLexikAccessTokenIssue()
    {
        $user = new User(
            new UserId('user-id'),
            new Email('user@example.com'),
            new Password('hashed-password')
        );

        $stubJwtToken = 'fake-jwt-token';

        $mockJwtTokenManager = $this->createMock(JwtTokenManagerInterface::class);
        $mockJwtTokenManager
            ->expects($this->once())
            ->method('create')
            ->with(
                $this->isInstanceOf(UserInterface::class)
            )
            ->willReturn($stubJwtToken);

        $lexikAccessTokenIssuer = new LexikAccessTokenIssuer($mockJwtTokenManager);

        $accessToken = $lexikAccessTokenIssuer->issue($user);

        $this->assertSame($stubJwtToken, $accessToken);

    }

}
