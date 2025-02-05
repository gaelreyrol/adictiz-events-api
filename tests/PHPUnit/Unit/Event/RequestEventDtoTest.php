<?php

namespace Adictiz\Tests\PHPUnit\Unit\Event;

use Adictiz\DTO\RequestEventDto;
use Adictiz\Entity\EventStatusEnum;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * @coversDefaultClass \Adictiz\DTO\RequestEventDto
 */
class RequestEventDtoTest extends KernelTestCase
{
    private ValidatorInterface $validator;

    protected function setUp(): void
    {
        $this->validator = self::getContainer()->get(ValidatorInterface::class);
    }

    /**
     * @dataProvider dtos
     *
     * @param array<string, string[]> $violations
     */
    public function testValidate(RequestEventDto $dto, array $violations = []): void
    {
        $list = $this->validator->validate($dto);
        $messages = [];
        foreach ($list as $violation) {
            $messages[$violation->getPropertyPath()][] = $violation->getMessage();
        }
        self::assertSame($violations, $messages);
    }

    /**
     * @return \Generator<array{
     *     0: RequestEventDto,
     *     1: array<string, string[]>,
     * }>
     */
    public static function dtos(): \Generator
    {
        yield [new RequestEventDto('title', 'description', new \DateTime(), (new \DateTime())->modify('+1 day'), EventStatusEnum::Draft), []];
        yield [new RequestEventDto('', 'description', new \DateTime(), (new \DateTime())->modify('+1 day'), EventStatusEnum::Draft), [
            'title' => [
                'This value should not be blank.',
                'This value is too short. It should have 5 characters or more.',
            ],
        ]];
        yield [new RequestEventDto('title', '', new \DateTime(), (new \DateTime())->modify('+1 day'), EventStatusEnum::Draft), [
            'description' => [
                'This value should not be blank.',
                'This value is too short. It should have 5 characters or more.',
            ],
        ]];
        yield [new RequestEventDto('title', 'description', new \DateTime()), []];
        yield [new RequestEventDto('title', 'description', new \DateTime(), (new \DateTime())->modify('-1 day'), EventStatusEnum::Draft), [
            'endDate' => ['End date must be greater than start date'],
        ]];
    }
}
