<?php

namespace Adictiz\Exception;

use Adictiz\Entity\ValueObject\EventId;

class EventUpdateFailureException extends AbstractUserException
{
    public function __construct(EventId $id, ?\Throwable $previous = null)
    {
        parent::__construct(message: sprintf('Failed to update event with id: %s', $id), previous: $previous);
    }
}
