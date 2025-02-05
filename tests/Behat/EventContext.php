<?php

namespace Adictiz\Tests\Behat;

use Adictiz\Entity\Event;
use Adictiz\Entity\EventStatusEnum;
use Adictiz\Entity\User;
use Adictiz\Entity\ValueObject\EventId;
use Adictiz\Entity\ValueObject\EventTitle;
use Adictiz\Service\EventService;
use Behat\Behat\Context\Context;
use Behat\Behat\Context\Environment\InitializedContextEnvironment;
use Behat\Behat\Hook\Scope\BeforeScenarioScope;
use Behat\Hook\BeforeScenario;
use Behat\Step\Given;

class EventContext implements Context
{
    private ?UserContext $userContext = null;

    public function __construct(private readonly EventService $eventService)
    {
    }

    #[BeforeScenario]
    public function gatherContexts(BeforeScenarioScope $scope): void
    {
        /** @var InitializedContextEnvironment $env */
        $env = $scope->getEnvironment();
        $this->userContext = $env->getContext(UserContext::class);
    }

    #[Given('I create an event :id, :title, :description, :startDate, :endDate, :status')]
    public function iCreateAnEventWithIdAs(string $id, string $title, string $description, string $startDate, string $endDate, string $status): void
    {
        $event = new Event(EventId::fromString($id), new EventTitle($title), $description, $this->getUser());
        $startDateTime = \DateTime::createFromFormat('Y-m-d', $startDate);
        if (false !== $startDateTime) {
            $event->setStartDate($startDateTime);
        }
        $endDateTime = \DateTime::createFromFormat('Y-m-d', $endDate);
        $event->setEndDate($endDateTime ?: null);
        $event->setStatus(EventStatusEnum::from($status));

        $this->eventService->create($event);
    }

    private function getUser(): User
    {
        if (false === $this->userContext instanceof UserContext) {
            throw new \RuntimeException('UserContext not found');
        }

        return $this->userContext->getUser();
    }
}
