<?php

namespace Adictiz\Entity;

use Adictiz\Entity\ValueObject\EventId;
use Adictiz\Entity\ValueObject\EventTitle;
use Adictiz\Repository\EventRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: EventRepository::class)]
class Event
{
    public function __construct(
        #[ORM\Embedded(columnPrefix: false)]
        private EventId $id,

        #[ORM\Embedded(columnPrefix: false)]
        private EventTitle $title,

        #[ORM\Column(type: Types::TEXT)]
        private string $description,

        #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'events')]
        #[ORM\JoinColumn(nullable: false)]
        private User $owner,

        #[ORM\Column(enumType: EventStatusEnum::class)]
        private EventStatusEnum $status = EventStatusEnum::Draft,

        #[ORM\Column(type: Types::DATE_MUTABLE)]
        private \DateTimeInterface $startDate = new \DateTimeImmutable(),

        #[ORM\Column(type: Types::DATE_MUTABLE, nullable: true)]
        private ?\DateTimeInterface $endDate = null,
    ) {
    }

    public function getId(): EventId
    {
        return $this->id;
    }

    public function getTitle(): EventTitle
    {
        return $this->title;
    }

    public function setTitle(EventTitle $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getStartDate(): \DateTimeInterface
    {
        return $this->startDate;
    }

    public function setStartDate(\DateTimeInterface $startDate): self
    {
        $this->startDate = $startDate;

        return $this;
    }

    public function getEndDate(): ?\DateTimeInterface
    {
        return $this->endDate;
    }

    public function setEndDate(?\DateTimeInterface $endDate): self
    {
        $this->endDate = $endDate;

        return $this;
    }

    public function getStatus(): ?EventStatusEnum
    {
        return $this->status;
    }

    public function setStatus(EventStatusEnum $status): self
    {
        $this->status = $status;

        return $this;
    }

    public function getOwner(): User
    {
        return $this->owner;
    }

    public function setOwner(User $owner): self
    {
        $this->owner = $owner;

        return $this;
    }
}
