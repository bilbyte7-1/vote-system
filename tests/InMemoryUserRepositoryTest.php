<?php
namespace App\Tests;

use App\Domain\User\Email;
use App\Domain\User\Password;
use App\Domain\User\User;
use App\Domain\User\UserId;
use App\Tests\Fakers\InMemoryUserRepository;
use PHPUnit\Framework\TestCase;

final class InMemoryUserRepositoryTest extends TestCase
{
    private $userRepository;
    private $user;
    private $entityManager;
    protected function setUp(): void
    {
        parent::setUp();

        $this->userRepository = new InMemoryUserRepository();

        $this->user = new User(
            new UserId('user-id'),
            new Email('user@example.com'),
            new Password('user-password'),
        );
        //$this->entityManager = $this->createMock(EntityManagerInterface::class);
    }

    public function testItSavesUser(): void
    {
        $this->userRepository->save($this->user);

        self::assertSame(
            $this->user,
            $this->userRepository->getSavedUser()
        );
    }

    public function testItExistsUser(): void
    {
        $this->userRepository->save($this->user);

        self::assertTrue(
            $this->userRepository->existsByEmail('user@example.com')
        );

        self::assertFalse(
            $this->userRepository->existsByEmail('unknown@example.com')
        );
    }

    public function testItFindUserByEmail(): void
    {
        $this->userRepository->save($this->user);

        self::assertSame(
            $this->user->getId(),
            $this->userRepository->findByEmail('user@example.com')->getId()
        );
    }
}
