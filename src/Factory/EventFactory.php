<?php

namespace Adictiz\Factory;

use Adictiz\Entity\Event;
use Adictiz\Entity\User;
use Adictiz\Entity\ValueObject\EventId;
use Adictiz\Entity\ValueObject\EventTitle;

class EventFactory
{
    public static function create(string $title, string $description, User $owner): Event
    {
        return new Event(new EventId(), new EventTitle($title), $description, $owner);
    }
}
