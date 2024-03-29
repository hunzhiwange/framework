<?php

declare(strict_types=1);

namespace Tests\Database\Ddd\Entity\Relation;

use Leevel\Database\Ddd\Entity;
use Leevel\Database\Ddd\Struct;

class Comment extends Entity
{
    public const TABLE = 'comment';

    public const ID = 'id';

    public const AUTO = 'id';

    #[Struct([
    ])]
    protected ?int $id = null;

    #[Struct([
    ])]
    protected ?string $title = null;

    #[Struct([
    ])]
    protected ?int $postId = null;

    #[Struct([
    ])]
    protected ?string $content = null;

    #[Struct([
    ])]
    protected ?string $createAt = null;
}
