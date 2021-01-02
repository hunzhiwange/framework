<?php

declare(strict_types=1);

namespace Tests\Database\Ddd\Entity\Relation;

use Leevel\Database\Ddd\Entity;
use Leevel\Database\Ddd\GetterSetter;

class PostContent extends Entity
{
    use GetterSetter;

    public const TABLE = 'post_content';

    public const ID = null;

    public const AUTO = null;

    public const STRUCT = [
        'post_id' => [
            self::READONLY => true,
        ],
        'content' => [],
    ];
}
