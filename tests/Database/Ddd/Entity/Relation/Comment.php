<?php

declare(strict_types=1);

namespace Tests\Database\Ddd\Entity\Relation;

use Leevel\Database\Ddd\Entity;
use Leevel\Database\Ddd\Struct;

class Comment extends Entity
{
    public const string TABLE = 'comment';

    public const string ID = 'id';

    public const string AUTO = 'id';

    #[Struct([])]
    public ?int $id = null {
        set(int|string|null $v) {
            $this->id = $this->set('id', $v);
        }
    }

    #[Struct([])]
    public ?string $title = null {
        set(string|null $v) {
            $this->title = $this->set('title', $v);
        }
    }

    #[Struct([])]
    public ?int $postId = null {
        set(int|string|null $v) {
            $this->postId = $this->set('postId', $v);
        }
    }

    #[Struct([])]
    public ?string $content = null {
        set(string|null $v) {
            $this->content = $this->set('content', $v);
        }
    }

    #[Struct([])]
    public ?string $createAt = null {
        set(string|null $v) {
            $this->createAt = $this->set('createAt', $v);
        }
    }
}
