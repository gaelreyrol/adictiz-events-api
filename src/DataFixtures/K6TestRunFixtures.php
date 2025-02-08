<?php

namespace Adictiz\DataFixtures;

use Adictiz\Entity\Event;
use Adictiz\Entity\User;
use Adictiz\Entity\ValueObject\EventId;
use Adictiz\Entity\ValueObject\EventTitle;
use Adictiz\Repository\UserRepository;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use Doctrine\Persistence\ObjectManager;

class K6TestRunFixtures extends Fixture implements FixtureGroupInterface
{
    public function __construct(private readonly UserRepository $userRepository)
    {
    }

    public function load(ObjectManager $manager): void
    {
        $user = $this->userRepository->findOneBy(['email.value' => 'k6@adictiz.com']);
        assert($user instanceof User);

        for ($i = 0; $i < 500; ++$i) {
            $event = new Event(new EventId(), new EventTitle('K6 Event '.$i), 'K6 Description', $user);
            $manager->persist($event);
        }

        $manager->flush();
    }

    public static function getGroups(): array
    {
        return ['k6'];
    }
}
