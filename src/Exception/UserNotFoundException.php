<?php

namespace Adictiz\Exception;

use Adictiz\Entity\ValueObject\UserId;

class UserNotFoundException extends AbstractUserException
{
    public function __construct(UserId $id)
    {
        parent::__construct(sprintf("User with id '%s' not found", $id));
    }
}
