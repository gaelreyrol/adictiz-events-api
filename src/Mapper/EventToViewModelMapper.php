<?php

namespace Adictiz\Mapper;

use Adictiz\Entity\Event;
use Adictiz\ViewModel\EventViewModel;
use Symfonycasts\MicroMapper\AsMapper;
use Symfonycasts\MicroMapper\MapperInterface;

#[AsMapper(from: Event::class, to: EventViewModel::class)]
class EventToViewModelMapper implements MapperInterface
{
    /**
     * @param array<mixed, mixed> $context
     *
     * @return object|EventViewModel
     */
    public function load(object $from, string $toClass, array $context): object
    {
        $event = $from;
        assert($event instanceof Event);

        return new EventViewModel(
            (string) $event->getId(),
            (string) $event->getTitle(),
            $event->getDescription(),
            $event->getStatus()->value,
            $event->getStartDate()->format('Y-m-d'),
            $event->getEndDate()?->format('Y-m-d'),
        );
    }

    /**
     * @param array<mixed, mixed> $context
     *
     * @return object|EventViewModel
     */
    public function populate(object $from, object $to, array $context): object
    {
        $event = $from;
        $viewModel = $to;

        assert($event instanceof Event);
        assert($viewModel instanceof EventViewModel);

        return $viewModel;
    }
}
