<?php

namespace Adictiz\Factory;

use Adictiz\Entity\User;
use Adictiz\Entity\ValueObject\UserEmail;
use Adictiz\Entity\ValueObject\UserId;

class UserFactory
{
    public static function create(string $email, string $password): User
    {
        return new User(new UserId(), new UserEmail($email), $password);
    }
}
