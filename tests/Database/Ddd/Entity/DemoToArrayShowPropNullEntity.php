<?php

declare(strict_types=1);

namespace Tests\Database\Ddd\Entity;

use Leevel\Database\Ddd\Entity;
use Leevel\Database\Ddd\Struct;

class DemoToArrayShowPropNullEntity extends Entity
{
    public const TABLE = 'test';

    public const ID = 'id';

    public const AUTO = 'id';

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
}
