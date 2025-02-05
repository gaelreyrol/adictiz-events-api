<?php

namespace Adictiz\Tests\PHPUnit\Unit\Event;

use Adictiz\Entity\ValueObject\EventId;
use Adictiz\Exception\EventNotFoundException;
use Adictiz\Service\EventService;
use Adictiz\Service\UserService;
use Adictiz\Tests\Factory\EventFactory;
use Adictiz\Tests\Factory\UserFactory;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

/**
 * @coversDefaultClass \Adictiz\Service\EventService
 */
class EventServiceTest extends KernelTestCase
{
    private UserService $userService;
    private EventService $eventService;

    protected function setUp(): void
    {
        $this->userService = self::getContainer()->get(UserService::class);
        $this->eventService = self::getContainer()->get(EventService::class);
    }

    public function testGetEvent(): void
    {
        $eventId = EventId::fromString('cb3ca884-1b37-4b96-bfb0-4ff612b912d4');

        self::expectExceptionObject(new EventNotFoundException($eventId));
        $this->eventService->get($eventId);
    }

    public function testCreateEvent(): void
    {
        $user = $this->userService->create(UserFactory::create('john@doe.com', 'password'));

        $event = $this->eventService->create(EventFactory::create('title', 'description', $user));
        self::assertSame($event, $this->eventService->get($event->getId()));
    }

    public function testUpdateEvent(): void
    {
        $user = $this->userService->create(UserFactory::create('john@doe.com', 'password'));
        $event = $this->eventService->create(EventFactory::create('title', 'description', $user));

        $event->setDescription('new description');
        $this->eventService->update($event);

        self::assertSame($event->getDescription(), $this->eventService->get($event->getId())->getDescription());
    }

    public function testDeleteEvent(): void
    {
        $user = $this->userService->create(UserFactory::create('john@doe.com', 'password'));
        $event = $this->eventService->create(EventFactory::create('title', 'description', $user));

        $this->eventService->delete($event);

        $this->expectExceptionObject(new EventNotFoundException($event->getId()));
        $this->eventService->get($event->getId());
    }
}
