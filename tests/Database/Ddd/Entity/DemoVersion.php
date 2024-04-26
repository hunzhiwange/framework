<?php

declare(strict_types=1);

namespace Tests\Database\Ddd\Entity;

use Leevel\Database\Ddd\Entity;
use Leevel\Database\Ddd\Struct;

class DemoVersion extends Entity
{
    public const string TABLE = 'test_version';

    public const string ID = 'id';

    public const string AUTO = 'id';

    public const string VERSION = 'version';

    #[Struct([
        self::READONLY => true,
    ])]
    protected ?int $id = null;

    #[Struct([
    ])]
    protected ?string $name = null;

    #[Struct([
    ])]
    protected ?string $availableNumber = null;

    #[Struct([
    ])]
    protected ?string $realNumber = null;

    #[Struct([
    ])]
    protected ?int $version = null;

    protected bool $enabledVersionFramework = true;
}
