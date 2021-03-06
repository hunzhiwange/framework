<?php

declare(strict_types=1);

namespace Tests\Database\Ddd\Entity;

use Leevel\Database\Ddd\Entity;
use Leevel\Database\Ddd\GetterSetter;

class DemoConstructPropBlackEntity extends Entity
{
    use GetterSetter;

    public const TABLE = 'test';

    public const ID = 'id';

    public const AUTO = 'id';

    public const STRUCT = [
        'id' => [
            self::READONLY             => true,
            self::CONSTRUCT_PROP_BLACK => true,
        ],
        'name' => [],
    ];
}
