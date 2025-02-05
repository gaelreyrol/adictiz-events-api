<?php

namespace Adictiz\Repository;

use Adictiz\Entity\Event;
use Adictiz\Entity\EventStatusEnum;
use Adictiz\Entity\ValueObject\UserId;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Event>
 */
class EventRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Event::class);
    }

    public function filterByOwnerCriteria(UserId $ownerId): Criteria
    {
        return Criteria::create()
            ->where(Criteria::expr()->eq('owner', $ownerId));
    }

    public function filterByStatusCriteria(EventStatusEnum $status): Criteria
    {
        return Criteria::create()
            ->where(Criteria::expr()->eq('status', $status));
    }

    /**
     * @return Paginator<Event>
     */
    public function paginateAllBy(?Criteria $criteria = null, int $page = 1, int $limit = 10): Paginator
    {
        if (null === $criteria) {
            $criteria = Criteria::create();
        }

        $qb = $this->createQueryBuilder('e');

        $qb->addCriteria($criteria)
            ->orderBy('e.startDate', 'ASC')
            ->setFirstResult($limit * ($page - 1))
            ->setMaxResults($limit);

        return new Paginator($qb->getQuery());
    }

    public function save(Event $event): void
    {
        $this->getEntityManager()->persist($event);
        $this->getEntityManager()->flush();
    }

    public function remove(Event $event): void
    {
        $this->getEntityManager()->remove($event);
        $this->getEntityManager()->flush();
    }
}
