<?php

namespace Adictiz\Controller;

use Adictiz\DTO\EventDto;
use Adictiz\Entity\Event;
use Adictiz\Entity\User;
use Adictiz\Security\EventVoter;
use Adictiz\Service\EventService;
use Adictiz\ViewModel\EventViewModel;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Routing\Requirement\Requirement;
use Symfony\Component\Security\Http\Attribute\CurrentUser;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfonycasts\MicroMapper\MicroMapperInterface;

#[Route('/api/events')]
class EventController extends AbstractController
{
    public function __construct(
        private readonly EventService $eventService,
        private readonly MicroMapperInterface $microMapper,
    ) {
    }

    #[Route(methods: ['POST'], format: 'json')]
    public function create(
        #[CurrentUser]
        User $user,
        #[MapRequestPayload]
        EventDto $dto,
    ): JsonResponse {
        $event = $this->microMapper->map($dto, Event::class, ['owner' => $user]);

        $this->eventService->create($event);

        return $this->json($this->microMapper->map($event, EventViewModel::class), Response::HTTP_CREATED);
    }

    #[Route(
        path: '/{id}',
        requirements: ['id' => Requirement::UUID_V4],
        methods: ['PUT'],
        format: 'json',
    )]
    #[IsGranted(EventVoter::UPDATE, 'event', 'Event not found', Response::HTTP_NOT_FOUND)]
    public function update(
        Event $event,
        #[MapRequestPayload]
        EventDto $dto,
    ): JsonResponse {
        $event = $this->microMapper->map($dto, Event::class, ['id' => $event->getId()]);

        $this->eventService->update($event);

        return $this->json($this->microMapper->map($event, EventViewModel::class));
    }

    #[Route(
        path: '/{id}',
        requirements: ['id' => Requirement::UUID_V4],
        methods: ['GET'],
        format: 'json',
    )]
    #[IsGranted(EventVoter::VIEW, 'event', 'Event not found', Response::HTTP_NOT_FOUND)]
    public function view(Event $event): JsonResponse
    {
        return $this->json($this->microMapper->map($event, EventViewModel::class));
    }

    #[Route(
        path: '/{id}',
        requirements: ['id' => Requirement::UUID_V4],
        methods: ['DELETE'],
        format: 'json',
    )]
    #[IsGranted(EventVoter::DELETE, 'event', 'Event not found', Response::HTTP_NOT_FOUND)]
    public function delete(Event $event): JsonResponse
    {
        $this->eventService->delete($event);

        return new JsonResponse(status: Response::HTTP_NO_CONTENT);
    }
}
