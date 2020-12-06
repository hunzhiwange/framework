<?php

declare(strict_types=1);

namespace Tests\Database\Ddd\Entity\Relation;

use Leevel\Database\Ddd\Entity;
use Leevel\Database\Ddd\GetterSetter;

class Comment extends Entity
{
    use GetterSetter;

    const TABLE = 'comment';

    const ID = 'id';

    const AUTO = 'id';

    const STRUCT = [
        'id'        => [],
        'title'     => [],
        'post_id'   => [],
        'content'   => [],
        'create_at' => [],
    ];
}
