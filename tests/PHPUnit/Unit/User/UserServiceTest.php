<?php

namespace Adictiz\Tests\PHPUnit\Unit\User;

use Adictiz\Entity\ValueObject\UserId;
use Adictiz\Exception\UserCreationFailureException;
use Adictiz\Exception\UserNotFoundException;
use Adictiz\Factory\UserFactory;
use Adictiz\Service\UserService;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

/**
 * @coversDefaultClass \Adictiz\Service\UserService
 */
class UserServiceTest extends KernelTestCase
{
    private UserService $userService;

    protected function setUp(): void
    {
        $this->userService = self::getContainer()->get(UserService::class);
    }

    public function testGetUser(): void
    {
        $userId = UserId::fromString('02fe4892-d197-483a-be86-b0417ef95e67');

        self::expectExceptionObject(new UserNotFoundException($userId));
        $this->userService->get($userId);
    }

    public function testCreateUser(): void
    {
        $user = $this->userService->create(UserFactory::create('john@doe.com', 'password'));
        self::assertSame($user, $this->userService->get($user->getId()));
    }

    public function testCreateDuplicatedUsers(): void
    {
        $this->userService->create(UserFactory::create('john@doe.com', 'password'));

        self::expectExceptionObject(new UserCreationFailureException());
        $this->userService->create(UserFactory::create('john@doe.com', 'password'));
    }
}
