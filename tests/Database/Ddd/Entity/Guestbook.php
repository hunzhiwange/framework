<?php

declare(strict_types=1);

namespace Tests\Database\Ddd\Entity;

use Leevel\Database\Ddd\Entity;
use Leevel\Database\Ddd\GetterSetter;

class Guestbook extends Entity
{
    use GetterSetter;

    public const TABLE = 'guest_book';

    public const ID = 'id';

    public const AUTO = 'id';

    public const STRUCT = [
        'id' => [
            self::READONLY => true,
        ],
        'name' => [],
        'content' => [],
        'create_at' => [],
    ];

    public const REPOSITORY = GuestbookRepository::class;
}
