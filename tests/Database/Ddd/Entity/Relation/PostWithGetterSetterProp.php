<?php

declare(strict_types=1);

namespace Tests\Database\Ddd\Entity\Relation;

use Leevel\Database\Ddd\Entity;
use Leevel\Database\Ddd\GetterSetterProp;

class PostWithGetterSetterProp extends Entity
{
    use GetterSetterProp;

    const TABLE = 'post';

    const ID = 'id';

    const AUTO = 'id';

    const STRUCT = [
        'id' => [
            self::READONLY           => true,
        ],
        'title'     => [],
        'user_id'   => [],
        'summary'   => [],
        'create_at' => [],
        'delete_at' => [
            self::CREATE_FILL => 0,
        ],
    ];

    private $_id;
    private $_title;
    private $_userId;
    private $_summary;
    private $_createAt;
    private $_deleteAt;
}
