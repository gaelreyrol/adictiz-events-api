<?php

namespace Adictiz\Tests\PHPUnit\Unit\Event;

use Adictiz\Entity\EventStatusEnum;
use Adictiz\Entity\ValueObject\UserId;
use Adictiz\Repository\EventRepository;
use Adictiz\Service\UserService;
use Adictiz\Tests\Factory\EventFactory;
use Adictiz\Tests\Factory\UserFactory;
use Doctrine\Common\Collections\Criteria;
use Doctrine\Common\Collections\Expr\Expression;
use Doctrine\ORM\Query\QueryExpressionVisitor;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\VarDumper\Test\VarDumperTestTrait;

/**
 * @coversDefaultClass \Adictiz\Repository\EventRepository
 */
class EventRepositoryTest extends KernelTestCase
{
    use VarDumperTestTrait;

    private UserService $userService;
    private EventRepository $eventRepository;

    protected function setUp(): void
    {
        $this->userService = self::getContainer()->get(UserService::class);
        $this->eventRepository = self::getContainer()->get(EventRepository::class);
    }

    public function testFilterByOwner(): void
    {
        $criteria = $this->eventRepository->filterByOwnerCriteria(new UserId());
        $expression = $criteria->getWhereExpression();
        self::assertInstanceOf(Expression::class, $expression);

        $this->assertDumpEquals(<<<'DUMP'
        Doctrine\ORM\Query\Expr\Comparison {
          #leftExpr: "e.owner"
          #operator: "="
          #rightExpr: ":owner"
        }
        DUMP, $expression->visit(new QueryExpressionVisitor(['e'])));
    }

    public function testFilterByStatus(): void
    {
        $criteria = $this->eventRepository->filterByStatusCriteria(EventStatusEnum::Draft);
        $expression = $criteria->getWhereExpression();
        self::assertInstanceOf(Expression::class, $expression);

        $this->assertDumpEquals(<<<'DUMP'
        Doctrine\ORM\Query\Expr\Comparison {
          #leftExpr: "e.status"
          #operator: "="
          #rightExpr: ":status"
        }
        DUMP, $expression->visit(new QueryExpressionVisitor(['e'])));
    }

    public function testPaginateAllByWithCriteria(): void
    {
        $criteria = Criteria::create()->where(Criteria::expr()->eq('title', 'Title'));
        $paginator = $this->eventRepository->paginateAllBy($criteria, 1, 1);

        self::assertEquals(<<<DQL
        SELECT e FROM Adictiz\Entity\Event e WHERE e.title = :title ORDER BY e.startDate ASC
        DQL, $paginator->getQuery()->getDQL());
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
