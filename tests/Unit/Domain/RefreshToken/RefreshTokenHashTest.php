<?php

namespace App\Tests\Unit\Domain\RefreshToken;

use App\Domain\RefreshToken\RefreshTokenHash;
use PHPUnit\Framework\TestCase;

class RefreshTokenHashTest extends TestCase
{
    public function testItRefreshesTokenHashCreated():void
    {
        $tokenHash = 'refresh-token-hash';
        $hash = new RefreshTokenHash($tokenHash);
        self::assertSame($tokenHash, $hash->getHash());
    }

    public function testItThrowsExceptionIfInvalidHash():void
    {
        $tokenHash = '';
        self::expectException(\InvalidArgumentException::class);
        new RefreshTokenHash($tokenHash);
    }
}
