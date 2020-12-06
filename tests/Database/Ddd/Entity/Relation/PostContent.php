<?php

declare(strict_types=1);

namespace Tests\Database\Ddd\Entity\Relation;

use Leevel\Database\Ddd\Entity;
use Leevel\Database\Ddd\GetterSetter;

class PostContent extends Entity
{
    use GetterSetter;

    const TABLE = 'post_content';

    const ID = null;

    const AUTO = null;

    const STRUCT = [
        'post_id' => [
            self::READONLY => true,
        ],
        'content' => [],
    ];
}
