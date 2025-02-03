<?php

namespace Adictiz\Tests\PHPUnit\Unit\User;

use Adictiz\Factory\UserFactory;
use Adictiz\Repository\UserRepository;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

/**
 * @coversDefaultClass \Adictiz\Repository\UserRepository
 */
class UserRepositoryTest extends KernelTestCase
{
    private UserRepository $userRepository;

    protected function setUp(): void
    {
        $this->userRepository = self::getContainer()->get(UserRepository::class);
    }

    public function testSave(): void
    {
        $user = UserFactory::create('john@doe.com', 'password');
        $this->userRepository->save($user);
        self::assertSame($user, $this->userRepository->find($user->getId()));
    }

    public function testUniqueEmailConstraint(): void
    {
        $this->userRepository->save(UserFactory::create('john@doe.com', 'password'));
        $user = UserFactory::create('john@doe.com', 'password');

        $this->expectException(UniqueConstraintViolationException::class);
        $this->userRepository->save($user);
    }
}
