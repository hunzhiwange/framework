<?php

declare(strict_types=1);

namespace Tests\Database\Ddd\Entity\Relation;

use Exception;
use Leevel\Database\Ddd\Entity;
use Leevel\Database\Ddd\GetterSetter;

class PostForReplace extends Entity
{
    use GetterSetter;

    public const TABLE = 'post';

    public const ID = 'id';

    public const AUTO = 'id';

    public const STRUCT = [
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

    public const DELETE_AT = 'delete_at';

    protected function updateReal(): self
    {
        throw new Exception('Update error');
    }
}
