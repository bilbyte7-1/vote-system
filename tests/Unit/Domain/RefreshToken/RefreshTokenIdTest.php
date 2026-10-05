<?php

namespace App\Tests\Unit\Domain\RefreshToken;

use App\Domain\RefreshToken\RefreshTokenId;
use PHPUnit\Framework\TestCase;

class RefreshTokenIdTest extends TestCase
{
    public function testItRefreshTokenIdCreated(): void
    {
        $id = 'refresh-token-id';
        $refreshTokenId = new RefreshTokenId($id);
        $this->assertSame($id, $refreshTokenId->getId());
    }

    public function testItThrowsExceptionWhenIdIsInvalid(): void
    {
        $id = '';
        $this->expectException(\InvalidArgumentException::class);
        new RefreshTokenId($id);
    }
}
