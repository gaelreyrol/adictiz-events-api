<?php

namespace Adictiz\DTO;

use Adictiz\Entity\EventStatusEnum;
use Symfony\Component\Validator\Constraints as Assert;

readonly class QueryEventDto
{
    public function __construct(
        #[Assert\GreaterThanOrEqual(value: 1)]
        public int $page = 1,
        #[Assert\GreaterThanOrEqual(value: 1)]
        public int $limit = 10,
        #[Assert\NotBlank(allowNull: true)]
        #[Assert\Type(type: EventStatusEnum::class)]
        public ?EventStatusEnum $status = null,
    ) {
    }
}
