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
    public ?int $postId = null
    {
        set (int|string|null $v) {
            $this->postId = $this->set('postId', $v);
        }
    }

    #[Struct([
    ])]
    public ?string $content = null
    {
        set (string|null $v) {
            $this->content = $this->set('content', $v);
        }
    }
}
