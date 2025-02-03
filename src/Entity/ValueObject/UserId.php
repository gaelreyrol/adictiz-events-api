<?php

namespace Adictiz\Entity\ValueObject;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Uid\AbstractUid;
use Symfony\Component\Uid\Uuid;

#[ORM\Embeddable]
final class UserId implements \Stringable
{
    #[ORM\Id]
    #[ORM\Column(name: 'id', type: Types::GUID)]
    private AbstractUid $value;

    public function __construct(?AbstractUid $value = null)
    {
        $this->value = $value ?? Uuid::v4();
    }

    public function __toString(): string
    {
        return (string) $this->value;
    }

    public function getValue(): AbstractUid
    {
        return $this->value;
    }

    public static function fromString(string $value): self
    {
        return new self(Uuid::fromString($value));
    }
}
