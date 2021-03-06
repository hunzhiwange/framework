<?php

declare(strict_types=1);

namespace Tests\Database\Ddd\Entity;

use Leevel\Database\Ddd\Entity;
use Leevel\Database\Ddd\GetterSetter;

class SoftDeleteNotFoundDeleteAtField extends Entity
{
    use GetterSetter;

    public const TABLE = 'demo';

    public const ID = 'id';

    public const AUTO = 'id';

    public const STRUCT = [
        'id' => [
            self::READONLY           => true,
        ],
    ];

    public const DELETE_AT = 'delete_at';
}
