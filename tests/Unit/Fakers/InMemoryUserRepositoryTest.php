<?php

namespace App\Tests\Unit\Fakers;

use App\Domain\User\User;
use App\Tests\Fakers\InMemoryUserRepository;
use App\Tests\Fixtures\UserFixture;
use PHPUnit\Framework\TestCase;

final class InMemoryUserRepositoryTest extends TestCase
{
    private InMemoryUserRepository $userRepository;
    private User $user;
    protected function setUp(): void
    {
        parent::setUp();

        $this->userRepository = new InMemoryUserRepository();

        $this->user = UserFixture::create();
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
