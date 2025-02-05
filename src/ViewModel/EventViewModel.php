<?php

namespace Adictiz\ViewModel;

final readonly class EventViewModel
{
    public function __construct(
        public string $id,
        public string $title,
        public string $description,
        public string $status,
        public string $startDate,
        public ?string $endDate = null,
    ) {
    }
}
