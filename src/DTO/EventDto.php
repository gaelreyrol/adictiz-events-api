<?php

namespace Adictiz\DTO;

use Adictiz\Entity\EventStatusEnum;
use Symfony\Component\Validator\Constraints as Assert;

class EventDto
{
    public function __construct(
        #[Assert\NotBlank]
        #[Assert\Length(min: 5, max: 255)]
        public string $title,
        #[Assert\NotBlank]
        #[Assert\Length(min: 5, max: 255)]
        public string $description,
        #[Assert\NotBlank]
        public \DateTimeInterface $startDate,
        #[Assert\NotBlank(allowNull: true)]
        #[Assert\GreaterThan(propertyPath: 'startDate', message: 'End date must be greater than start date')]
        public ?\DateTimeInterface $endDate = null,
        #[Assert\Type(type: EventStatusEnum::class)]
        public EventStatusEnum $status = EventStatusEnum::Draft,
    ) {
    }
}
