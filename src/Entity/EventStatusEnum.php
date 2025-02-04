<?php

namespace Adictiz\Entity;

enum EventStatusEnum: string
{
    case Draft = 'draft';
    case Published = 'published';
    case Archived = 'archived';
}
