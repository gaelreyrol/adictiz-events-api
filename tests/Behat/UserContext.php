<?php

namespace Adictiz\Tests\Behat;

use Adictiz\Entity\User;
use Adictiz\Service\UserService;
use Adictiz\Tests\Factory\UserFactory;
use Behat\Behat\Context\Context;
use Behat\Step\Given;

class UserContext implements Context
{
    private ?User $user = null;

    public function __construct(
        private readonly UserService $userService,
    ) {
    }

    #[Given('an existing user with email :email and password :password')]
    public function anExistingUserWithEmailAndPassword(string $email, string $password): void
    {
        $this->user = $this->userService->create(UserFactory::create($email, $password));
    }

    public function getUser(): User
    {
        if (false === $this->user instanceof User) {
            throw new \RuntimeException('User not found');
        }

        return $this->user;
    }
}
