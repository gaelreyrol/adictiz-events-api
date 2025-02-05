<?php

namespace Adictiz\Tests\Behat;

use Behat\Step\Given;
use Behat\Step\Then;
use Behatch\Context\RestContext;
use Behatch\HttpCall\Request;
use Behatch\Json\Json;
use Behatch\Json\JsonInspector;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\Uid\Uuid;
use Symfony\Component\Uid\UuidV4;

final class ApiContext extends RestContext
{
    private JsonInspector $inspector;

    public function __construct(
        #[Autowire('@behatch.http_call.request')]
        Request $request,
    ) {
        parent::__construct($request);
        $this->inspector = new JsonInspector('javascript');
    }

    #[Given('I set the Authorization header for the user with email :email and password :password')]
    public function iSetTheAuthorizationHeaderForTheUserWithEmailAndPassword(string $email, string $password): void
    {
        $this->request->setHttpHeader('Content-Type', 'application/json');
        $response = $this->request->send(
            method: 'POST',
            url: $this->locatePath('/api/login'),
            content: json_encode(['username' => $email, 'password' => $password], JSON_THROW_ON_ERROR),
        );
        $data = \json_decode($response->getContent(), true, 512, JSON_THROW_ON_ERROR);

        assert(is_array($data));
        $this->assertArrayHasKey('token', $data);
        assert(is_string($data['token']));

        $this->request->setHttpHeader('Authorization', sprintf('Bearer %s', $data['token']));
    }

    protected function getJson(): Json
    {
        return new Json($this->request->getContent());
    }

    #[Then('the JSON node :node should be an UUID v4')]
    public function theJsonNodeShouldBeAnUUIDV4(string $node): void
    {
        $json = $this->getJson();

        $actual = $this->inspector->evaluate($json, $node);

        assert(is_string($actual));
        $uuid = Uuid::fromString($actual);

        $this->assertTrue($uuid instanceof UuidV4);
    }
}
