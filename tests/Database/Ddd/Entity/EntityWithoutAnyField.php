<?php

declare(strict_types=1);

namespace Tests\Database\Ddd\Entity;

use Leevel\Database\Ddd\Entity;

class EntityWithoutAnyField extends Entity
{
    public const string TABLE = 'test';

    public const null ID = null;

    public const null AUTO = null;
}
