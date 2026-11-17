<?php

namespace App\Tests\Unit\Infrastructure;

use App\Infrastructure\Security\RandomRefreshTokenIssuer;
use PHPUnit\Framework\TestCase;

class RandomRefreshTokenIssuerTest extends TestCase
{
    public function testIssueRefreshToken(): void
    {
        $refreshToken = new RandomRefreshTokenIssuer();
        $token = $refreshToken->issue();

        self::assertSame(64, strlen($token));
        self::assertMatchesRegularExpression('/^[0-9a-f]{64}$/', $token);
    }
}
