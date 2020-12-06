<?php

declare(strict_types=1);

namespace Tests\Database\Ddd\Entity;

use Leevel\Database\Ddd\Entity;
use Leevel\Database\Ddd\GetterSetter;

class Guestbook extends Entity
{
    use GetterSetter;

    const TABLE = 'guest_book';

    const ID = 'id';

    const AUTO = 'id';

    const STRUCT = [
        'id' => [
            self::READONLY             => true,
        ],
        'name'      => [],
        'content'   => [],
        'create_at' => [],
    ];

    const REPOSITORY = GuestbookRepository::class;
}
