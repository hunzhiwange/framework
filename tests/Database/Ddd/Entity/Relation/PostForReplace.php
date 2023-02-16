<?php

declare(strict_types=1);

namespace Tests\Database\Ddd\Entity\Relation;

use Leevel\Database\Ddd\Entity;
use Leevel\Database\Ddd\Struct;

class PostForReplace extends Entity
{
    public const TABLE = 'post';

    public const ID = 'id';

    public const AUTO = 'id';

    public const STRUCT = [
        'id' => [
            self::READONLY => true,
        ],
        'title' => [],
        'user_id' => [],
        'summary' => [],
        'create_at' => [],
        'delete_at' => [
            self::CREATE_FILL => 0,
        ],
    ];

    public const DELETE_AT = 'delete_at';

    #[Struct([
        self::READONLY => true,
    ])]
    protected ?int $id = null;

    #[Struct([
    ])]
    protected ?string $title = null;

    #[Struct([
    ])]
    protected ?int $userId = null;

    #[Struct([
    ])]
    protected ?string $summary = null;

    #[Struct([
    ])]
    protected ?int $createAt = null;

    #[Struct([
        self::CREATE_FILL => 0,
    ])]
    protected ?int $deleteAt = null;

    protected function updateReal(): self
    {
        throw new \Exception('Update error');
    }
}
