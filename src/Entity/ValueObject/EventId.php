<?php

namespace Adictiz\Entity\ValueObject;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Uid\AbstractUid;
use Symfony\Component\Uid\Uuid;

#[ORM\Embeddable]
final class EventId implements \Stringable
{
    #[ORM\Id]
    #[ORM\Column(name: 'id', type: Types::GUID)]
    private string $value;

    public function __construct(?AbstractUid $value = null)
    {
        $this->value = $value?->toRfc4122() ?? Uuid::v4()->toRfc4122();
    }

    public function __toString(): string
    {
        return $this->value;
    }

    public static function fromString(string $value): self
    {
        return new self(Uuid::fromString($value));
    }
}
