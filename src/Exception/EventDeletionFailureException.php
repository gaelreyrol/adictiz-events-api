<?php

namespace Adictiz\Exception;

use Adictiz\Entity\ValueObject\EventId;

class EventDeletionFailureException extends AbstractUserException
{
    public function __construct(EventId $id, ?\Throwable $previous = null)
    {
        parent::__construct(message: sprintf('Failed to delete event with id: %s', $id), previous: $previous);
    }
}
