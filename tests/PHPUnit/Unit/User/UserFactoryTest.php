<?php

namespace Adictiz\Tests\PHPUnit\Unit\User;

use Adictiz\Factory\UserFactory;
use PHPUnit\Framework\TestCase;

/**
 * @coversDefaultClass \Adictiz\Factory\UserFactory
 */
class UserFactoryTest extends TestCase
{
    /**
     * @dataProvider users
     */
    public function testCreate(string $email, string $password, string $exception): void
    {
        $this->expectExceptionMessage($exception);

        UserFactory::create($email, $password);
    }

    public static function users(): \Generator
    {
        yield ['john@doe.com', '', 'Value "" is empty, but non empty value was expected.'];
        yield ['john@doe', 'password', 'Value "john@doe" was expected to be a valid e-mail address.'];
    }
}
