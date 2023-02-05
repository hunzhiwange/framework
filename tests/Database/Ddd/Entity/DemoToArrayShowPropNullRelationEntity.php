<?php

declare(strict_types=1);

namespace Tests\Database\Ddd\Entity;

use Leevel\Database\Ddd\Entity;
use Leevel\Database\Ddd\GetterSetter;

class DemoToArrayShowPropNullRelationEntity extends Entity
{
    use GetterSetter;

    public const TABLE = 'test';

    public const ID = 'id';

    public const AUTO = 'id';

    public const STRUCT = [
        'id' => [],
        'name' => [],
        'description' => [],
        'address' => [
            self::SHOW_PROP_NULL => '',
        ],
        'foo_bar' => [
            self::SHOW_PROP_NULL => null,
        ],
        'hello' => [
            self::SHOW_PROP_NULL => 'default_value',
        ],
        'target' => [
            self::HAS_ONE => DemoToArrayShowPropNullRelationTargetEntity::class,
            self::SOURCE_KEY => 'id',
            self::TARGET_KEY => 'id',
        ],
    ];
}
