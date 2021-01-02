<?php

declare(strict_types=1);

namespace Tests\Database\Ddd\Entity;

use Leevel\Database\Ddd\Entity;
use Leevel\Database\Ddd\GetterSetter;

class EntityWithoutAnyField extends Entity
{
    use GetterSetter;

    public const TABLE = 'test';

    public const ID = null;

    public const AUTO = null;

    public const STRUCT = [];
}
