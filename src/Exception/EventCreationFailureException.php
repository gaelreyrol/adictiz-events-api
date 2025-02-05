<?php

namespace Adictiz\Exception;

class EventCreationFailureException extends AbstractUserException
{
    public function __construct(?\Throwable $previous = null)
    {
        parent::__construct(message: 'Event creation failed', previous: $previous);
    }
}
