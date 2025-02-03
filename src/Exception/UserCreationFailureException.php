<?php

namespace Adictiz\Exception;

class UserCreationFailureException extends AbstractUserException
{
    public function __construct(?\Throwable $previous = null)
    {
        parent::__construct('User creation failed', 0, $previous);
    }
}
