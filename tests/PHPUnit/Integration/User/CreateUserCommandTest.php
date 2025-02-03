<?php

declare(strict_types=1);

namespace Adictiz\Tests\PHPUnit\Integration\User;

use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Console\Tester\CommandTester;

/**
 * @coversDefaultClass \Adictiz\Command\CreateUserCommand
 */
class CreateUserCommandTest extends KernelTestCase
{
    public function testExecute(): void
    {
        self::bootKernel();
        $application = new Application(self::$kernel);

        $command = $application->find('adictiz:user:create');
        $commandTester = new CommandTester($command);
        $commandTester->execute([
            'email' => 'john@doe.com',
            'password' => 'password',
        ]);

        $commandTester->assertCommandIsSuccessful();

        $output = $commandTester->getDisplay();
        $this->assertStringContainsString('The user has been created successfully!', $output);
    }
}
