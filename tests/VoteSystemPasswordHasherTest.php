<?php

namespace App\Tests;

use \App\Application\Interfaces\PasswordHasherInterface;
use Symfony\Component\PasswordHasher\PasswordHasherInterface as SymfonyPasswordHasher;
use App\Infrastructure\Security\VoteSystemPasswordHasher ;
use \PHPUnit\Framework\TestCase;

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
