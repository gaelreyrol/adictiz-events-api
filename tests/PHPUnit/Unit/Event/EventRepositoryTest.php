<?php

namespace Adictiz\Tests\PHPUnit\Unit\Event;

use Adictiz\Repository\EventRepository;
use Adictiz\Service\UserService;
use Adictiz\Tests\Factory\EventFactory;
use Adictiz\Tests\Factory\UserFactory;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

/**
 * @coversDefaultClass \Adictiz\Repository\EventRepository
 */
class EventRepositoryTest extends KernelTestCase
{
    private UserService $userService;
    private EventRepository $eventRepository;

    protected function setUp(): void
    {
        $this->userService = self::getContainer()->get(UserService::class);
        $this->eventRepository = self::getContainer()->get(EventRepository::class);
    }

    public function testSave(): void
    {
        $user = $this->userService->create(UserFactory::create('john@doe.com', 'password'));
        $event = EventFactory::create('title', 'description', $user);
        $this->eventRepository->save($event);
        self::assertSame($event, $this->eventRepository->find($event->getId()));
    }

    public function testRemove(): void
    {
        $user = $this->userService->create(UserFactory::create('john@doe.com', 'password'));
        $event = EventFactory::create('title', 'description', $user);
        $this->eventRepository->save($event);
        $this->eventRepository->remove($event);

        self::assertNull($this->eventRepository->find($event->getId()));
    }
}
