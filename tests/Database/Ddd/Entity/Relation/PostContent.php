<?php

declare(strict_types=1);

namespace Tests\Database\Ddd\Entity\Relation;

use Leevel\Database\Ddd\Entity;
use Leevel\Database\Ddd\Struct;

class PostContent extends Entity
{
    public const string TABLE = 'post_content';

    public const null ID = null;

    public const null AUTO = null;

    #[Struct([
        self::READONLY => true,
    ])]
    protected ?int $postId = null;

    #[Struct([
    ])]
    protected ?string $content = null;
}
