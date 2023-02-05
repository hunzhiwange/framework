<?php

declare(strict_types=1);

namespace Tests\Database\Ddd\Entity;

use Leevel\Database\Ddd\Entity;
use Leevel\Database\Ddd\GetterSetter;

class DemoVersion extends Entity
{
    use GetterSetter;

    public const TABLE = 'test_version';

    public const ID = 'id';

    public const AUTO = 'id';

    public const STRUCT = [
        'id' => [
            self::READONLY => true,
        ],
        'name' => [],
        'available_number' => [],
        'real_number' => [],
        'version' => [],
    ];

    public const VERSION = 'version';

    protected bool $version = true;
}
