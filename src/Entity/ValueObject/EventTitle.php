<?php

namespace Adictiz\Entity\ValueObject;

use Assert\Assertion;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Embeddable]
final class EventTitle implements \Stringable
{
    /**
     * @var non-empty-string
     */
    #[ORM\Column(name: 'title', type: Types::STRING, length: 255)]
    private string $value;

    public function __construct(string $value)
    {
        Assertion::notEmpty($value);
        Assertion::minLength($value, 5);
        $this->value = $value;
    }

    /**
     * @return non-empty-string
     */
    public function __toString(): string
    {
        return $this->value;
    }
}
