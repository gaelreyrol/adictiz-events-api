<?php

namespace Adictiz\Exception;

use Adictiz\Entity\ValueObject\EventId;

class EventNotFoundException extends AbstractUserException
{
    public function __construct(EventId $id)
    {
        parent::__construct(sprintf("Event with id '%s' not found", $id));
    }
}
