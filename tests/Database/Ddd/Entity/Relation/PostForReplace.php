<?php

declare(strict_types=1);

namespace Tests\Database\Ddd\Entity\Relation;

use Exception;
use Leevel\Database\Ddd\Entity;
use Leevel\Database\Ddd\GetterSetter;

class PostForReplace extends Entity
{
    use GetterSetter;

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

    const DELETE_AT = 'delete_at';

    protected function updateReal(): self
    {
        throw new Exception('Update error');
    }
}
