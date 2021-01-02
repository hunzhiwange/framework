<?php

declare(strict_types=1);

namespace Tests\Database\Ddd\Entity;

use Leevel\Database\Ddd\Entity;
use Leevel\Database\Ddd\GetterSetterProp;

class DemoPropErrorEntity extends Entity
{
    use GetterSetterProp;

    public const TABLE = 'error';

    public const ID = 'id';

    public const AUTO = 'id';

    public const STRUCT = [
        'id' => [
            self::READONLY           => true,
        ],
        'title'     => [],
        'name'      => [],
    ];
}
