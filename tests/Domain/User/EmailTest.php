<?php

namespace App\Tests\Domain\User;

use App\Domain\User\Email;
use InvalidArgumentException;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

final class EmailTest extends TestCase
{
    public function testItCreatesEmail(): void
    {
        $email = new Email('user@example.com');

        self::assertSame(
            'user@example.com',
            $email->getEmail(),
        );
    }

    public function testItTrimsEmail(): void
    {
        $email = new Email('  user@example.com  ');

        self::assertSame(
            'user@example.com',
            $email->getEmail(),
        );
    }

    public function testItConvertsEmailToLowercase(): void
    {
        $email = new Email('User@Example.COM');

        self::assertSame(
            'user@example.com',
            $email->getEmail(),
        );
    }

    public function testItTrimsAndConvertsEmailToLowercase(): void
    {
        $email = new Email('  User@Example.COM  ');

        self::assertSame(
            'user@example.com',
            $email->getEmail(),
        );
    }

    #[DataProvider('emptyEmailProvider')]
    public function testItRejectsEmptyEmail(string $value): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Email cannot be empty.');

        new Email($value);
    }

    /**
     * @return iterable<string, array{string}>
     */
    public static function emptyEmailProvider(): array
    {
        return [
            'empty string' => [''],
            'spaces' => ['   '],
            'tabs' => ["\t\t"],
            'line breaks' => ["\n\r"],
        ];
    }

    #[DataProvider('invalidEmailProvider')]
    public function testItRejectsInvalidEmail(string $value): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage(
            sprintf('"%s" is not a valid email.', mb_strtolower(trim($value))),
        );

        new Email($value);
    }

    /**
     * @return iterable<string, array{string}>
     */
    public static function invalidEmailProvider(): array
    {
        return [
            'without at sign' => ['user.example.com'],
            'without local part' => ['@example.com'],
            'without domain' => ['user@'],
            'plain text' => ['not-an-email'],
            'spaces inside' => ['user @example.com'],
        ];
    }
}
