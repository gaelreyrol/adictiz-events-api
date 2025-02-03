<?php

namespace Adictiz\Tests\Behat;

use Behat\Behat\Context\Context;
use Behat\Step\Then;
use Behat\Step\When;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\KernelInterface;

final class DemoContext implements Context
{
    private ?Response $response = null;

    public function __construct(private KernelInterface $kernel)
    {
    }

    #[When('a demo scenario sends a request to :path')]
    public function aDemoScenarioSendsARequestTo(string $path): void
    {
        $this->response = $this->kernel->handle(Request::create($path, 'GET'));
    }

    #[Then('the response should be received')]
    public function theResponseShouldBeReceived(): void
    {
        if (false === $this->response instanceof Response) {
            throw new \RuntimeException('No response received');
        }
    }
}
