<?php

declare(strict_types=1);

namespace Tests\Database\Ddd\Entity;

use Leevel\Database\Ddd\Entity;
use Leevel\Database\Ddd\Struct;

class DemoToArrayBlackEntity extends Entity
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
        self::SHOW_PROP_BLACK => true,
    ])]
    protected ?string $description = null;

    #[Struct([
    ])]
    protected ?string $address = null;

    #[Struct([
        self::SHOW_PROP_BLACK => true,
    ])]
    protected ?string $fooBar = null;

    #[Struct([
    ])]
    protected ?string $hello = null;
}
