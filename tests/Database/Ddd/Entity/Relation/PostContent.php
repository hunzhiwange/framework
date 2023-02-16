<?php

declare(strict_types=1);

namespace Tests\Database\Ddd\Entity\Relation;

use Leevel\Database\Ddd\Entity;
use Leevel\Database\Ddd\Struct;

class PostContent extends Entity
{
    public const TABLE = 'post_content';

    public const ID = null;

    public const AUTO = null;

    public const STRUCT = [
        'post_id' => [
            self::READONLY => true,
        ],
        'content' => [],
    ];

    #[Struct([
        self::READONLY => true,
    ])]
    protected ?int $postId = null;

    #[Struct([
    ])]
    protected ?string $content = null;
}
