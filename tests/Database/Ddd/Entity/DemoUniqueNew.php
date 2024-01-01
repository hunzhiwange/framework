<?php

declare(strict_types=1);

namespace Tests\Database\Ddd\Entity;

use Leevel\Database\Ddd\Entity;
use Leevel\Database\Ddd\Struct;

class DemoUniqueNew extends Entity
{
    public const TABLE = 'test_unique';

    public const ID = 'id';

    public const AUTO = 'id';

    /**
     * Unique key.
     */
    public const UNIQUE = [
        ['identity'],
    ];

    #[Struct([
        self::READONLY => true,
    ])]
    protected ?int $id = null;

    #[Struct([
    ])]
    protected ?string $name = null;

    #[Struct([
    ])]
    protected ?string $createAt = null;

    #[Struct([
    ])]
    protected ?string $identity = null;
}
