<?php

namespace Adictiz\Tests\Behat;

use Adictiz\Factory\UserFactory;
use Adictiz\Service\UserService;
use Behat\Behat\Context\Context;
use Behat\Step\Given;

class UserContext implements Context
{
    public function __construct(
        private readonly UserService $userService,
    ) {
    }

    #[Given('an existing user with email :email and password :password')]
    public function anExistingUserWithEmailAndPassword(string $email, string $password): void
    {
        $this->userService->create(UserFactory::create($email, $password));
    }
}
