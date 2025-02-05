<?php

namespace Adictiz\Mapper;

use Adictiz\DTO\RequestEventDto;
use Adictiz\Entity\Event;
use Adictiz\Entity\User;
use Adictiz\Entity\ValueObject\EventId;
use Adictiz\Entity\ValueObject\EventTitle;
use Adictiz\Service\EventService;
use Symfonycasts\MicroMapper\AsMapper;
use Symfonycasts\MicroMapper\MapperInterface;

#[AsMapper(from: RequestEventDto::class, to: Event::class)]
class RequestEventDtoToEntityMapper implements MapperInterface
{
    public function __construct(private readonly EventService $eventService)
    {
    }

    /**
     * @param array{
     *     id?: EventId,
     *     owner?: User,
     * } $context
     *
     * @return object|Event
     */
    public function load(object $from, string $toClass, array $context): object
    {
        $dto = $from;
        assert($dto instanceof RequestEventDto);

        if (array_key_exists('id', $context)) {
            return $this->eventService->get($context['id']);
        }

        if (false === array_key_exists('owner', $context)) {
            throw new \InvalidArgumentException('Missing "owner" in context');
        }

        return new Event(new EventId(), new EventTitle($dto->title), $dto->description, $context['owner']);
    }

    /**
     * @param array{
     *     id?: EventId,
     *     owner?: User,
     * } $context
     *
     * @return object|Event
     */
    public function populate(object $from, object $to, array $context): object
    {
        $dto = $from;
        $event = $to;

        assert($dto instanceof RequestEventDto);
        assert($event instanceof Event);

        $event->setTitle(new EventTitle($dto->title));
        $event->setDescription($dto->description);
        $event->setStartDate($dto->startDate);
        $event->setEndDate($dto->endDate);
        $event->setStatus($dto->status);

        return $event;
    }
}
