<?php

declare(strict_types=1);

namespace Tests\Database\Ddd\Entity\Relation;

use Leevel\Database\Ddd\Entity;
use Leevel\Database\Ddd\GetterSetter;

class Comment extends Entity
{
    use GetterSetter;

    public const TABLE = 'comment';

    public const ID = 'id';

    public const AUTO = 'id';

    public const STRUCT = [
        'id'        => [],
        'title'     => [],
        'post_id'   => [],
        'content'   => [],
        'create_at' => [],
    ];
}
