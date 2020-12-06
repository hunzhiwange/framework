<?php

declare(strict_types=1);

namespace Tests\Database\Ddd\Entity;

use Leevel\Database\Ddd\Entity;
use Leevel\Database\Ddd\GetterSetter;

class SoftDeleteNotFoundDeleteAtField extends Entity
{
    use GetterSetter;

    const TABLE = 'demo';

    const ID = 'id';

    const AUTO = 'id';

    const STRUCT = [
        'id' => [
            self::READONLY           => true,
        ],
    ];

    const DELETE_AT = 'delete_at';
}
