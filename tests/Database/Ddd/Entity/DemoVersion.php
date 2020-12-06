<?php

declare(strict_types=1);

namespace Tests\Database\Ddd\Entity;

use Leevel\Database\Ddd\Entity;
use Leevel\Database\Ddd\GetterSetter;

class DemoVersion extends Entity
{
    use GetterSetter;

    const TABLE = 'test_version';

    const ID = 'id';

    const AUTO = 'id';

    const STRUCT = [
        'id' => [
            self::READONLY             => true,
        ],
        'name'                   => [],
        'available_number'       => [],
        'real_number'            => [],
        'version'                => [],
    ];

    const VERSION = 'version';

    protected bool $version = true;
}
