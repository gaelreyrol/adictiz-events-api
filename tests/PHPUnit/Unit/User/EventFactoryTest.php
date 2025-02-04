<?php

namespace Adictiz\Tests\PHPUnit\Unit\User;

use Adictiz\Factory\EventFactory;
use Adictiz\Factory\UserFactory;
use PHPUnit\Framework\TestCase;

/**
 * @coversDefaultClass \Adictiz\Factory\EventFactory
 */
class EventFactoryTest extends TestCase
{
    /**
     * @dataProvider events
     */
    public function testCreate(string $title, string $description, string $exception): void
    {
        $this->expectExceptionMessage($exception);

        $user = UserFactory::create('john@doe.com', 'password');

        EventFactory::create($title, $description, $user);
    }

    public static function events(): \Generator
    {
        yield ['', '', 'Value "" is empty, but non empty value was expected.'];
        yield ['this', '', 'Value "this" is too short, it should have at least 5 characters, but only has 4 characters.'];
    }
}
