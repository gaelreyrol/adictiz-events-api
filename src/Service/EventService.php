<?php

namespace Adictiz\Service;

use Adictiz\Entity\Event;
use Adictiz\Entity\EventStatusEnum;
use Adictiz\Entity\User;
use Adictiz\Entity\ValueObject\EventId;
use Adictiz\Exception\EventCreationFailureException;
use Adictiz\Exception\EventDeletionFailureException;
use Adictiz\Exception\EventNotFoundException;
use Adictiz\Exception\EventUpdateFailureException;
use Adictiz\Repository\EventRepository;
use Doctrine\Common\Collections\Criteria;
use Psr\Log\LoggerInterface;

final readonly class EventService
{
    public function __construct(
        private EventRepository $eventRepository,
        private LoggerInterface $logger,
    ) {
    }

    /**
     * @return \Generator<Event>
     */
    public function findEventsForOwner(
        User $owner,
        ?EventStatusEnum $status = null,
        int $page = 1,
        int $limit = 10,
    ): \Generator {
        $criteria = Criteria::create();
        $ownerCriteria = $this->eventRepository->filterByOwnerCriteria($owner->getId());

        /* @phpstan-ignore-next-line */
        $criteria->where($ownerCriteria->getWhereExpression());

        if ($status instanceof EventStatusEnum) {
            $statusCriteria = $this->eventRepository->filterByStatusCriteria($status);
            /* @phpstan-ignore-next-line */
            $criteria->andWhere($statusCriteria->getWhereExpression());
        }

        $paginator = $this->eventRepository->paginateAllBy($criteria, $page, $limit);

        foreach ($paginator as $event) {
            yield $event;
        }
    }

    /**
     * @throws EventNotFoundException
     */
    public function get(EventId $id): Event
    {
        $event = $this->eventRepository->find($id);
        if (false === $event instanceof Event) {
            throw new EventNotFoundException($id);
        }

        return $event;
    }

    /**
     * @throws EventCreationFailureException
     */
    public function create(Event $event): Event
    {
        try {
            $this->eventRepository->save($event);
        } catch (\Exception $exception) {
            $this->logger->error('Failed to create event', [
                'exception' => $exception->getMessage(),
            ]);
            throw new EventCreationFailureException($exception);
        }

        return $event;
    }

    /**
     * @throws EventUpdateFailureException
     */
    public function update(Event $event): Event
    {
        try {
            $this->eventRepository->save($event);
        } catch (\Exception $exception) {
            $this->logger->error('Failed to update event', [
                'event_id' => (string) $event->getId(),
                'exception' => $exception->getMessage(),
            ]);
            throw new EventUpdateFailureException($event->getId(), $exception);
        }

        return $event;
    }

    /**
     * @throws EventDeletionFailureException
     */
    public function delete(Event $event): void
    {
        try {
            $this->eventRepository->remove($event);
        } catch (\Exception $exception) {
            $this->logger->error('Failed to delete event', [
                'event_id' => (string) $event->getId(),
                'exception' => $exception->getMessage(),
            ]);
            throw new EventDeletionFailureException($event->getId(), $exception);
        }
    }
}
