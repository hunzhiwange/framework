<?php

declare(strict_types=1);

namespace Tests\Database\Ddd\Entity;

use Leevel\Database\Ddd\Entity;
use Leevel\Database\Ddd\Struct;

class DemoToArrayShowPropNullRelationEntity extends Entity
{
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

    #[Struct([
    ])]
    protected ?int $id = null;

    #[Struct([
    ])]
    protected ?string $name = null;

    #[Struct([
    ])]
    protected ?string $description = null;

    #[Struct([
        self::SHOW_PROP_NULL => '',
    ])]
    protected ?string $address = null;

    #[Struct([
        self::SHOW_PROP_NULL => null,
    ])]
    protected ?string $fooBar = null;

    #[Struct([
        self::SHOW_PROP_NULL => 'default_value',
    ])]
    protected ?string $hello = null;

    #[Struct([
        self::HAS_ONE => DemoToArrayShowPropNullRelationTargetEntity::class,
        self::SOURCE_KEY => 'id',
        self::TARGET_KEY => 'id',
    ])]
    protected ?string $target = null;
}
