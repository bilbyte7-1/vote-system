<?php

namespace App\Tests\Unit\Infrastructure;

use App\Infrastructure\Security\VoteSystemPasswordHasher;
use PHPUnit\Framework\TestCase;
use Symfony\Component\PasswordHasher\PasswordHasherInterface as SymfonyPasswordHasher;

class VoteSystemPasswordHasherTest extends TestCase
{
    public function testPasswordHasher()
    {
        $plainPassword = 'strong-password';
        $mockHasher = self::createMock(SymfonyPasswordHasher::class);
        $mockHasher
            ->expects(self::once())
            ->method('hash')
            ->with($plainPassword)
            ->willReturn('hashed-password');

        $hasher = new VoteSystemPasswordHasher($mockHasher);
        $result = $hasher->hash($plainPassword);

        self::assertSame('hashed-password', $result);
    }
}
