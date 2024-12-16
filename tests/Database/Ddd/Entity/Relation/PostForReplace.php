<?php

declare(strict_types=1);

namespace Tests\Database\Ddd\Entity\Relation;

use Leevel\Database\Ddd\Entity;
use Leevel\Database\Ddd\Struct;

class PostForReplace extends Entity
{
    public const string TABLE = 'post';

    public const string ID = 'id';

    public const string AUTO = 'id';

    public const string DELETE_AT = 'delete_at';

    #[Struct([
        self::READONLY => true,
    ])]
    public ?int $id = null
    {
        set (int|string|null $v) {
            $this->id = $this->set('id', $v);
        }
    }

    #[Struct([
    ])]
    public ?string $title = null
    {
        set (string|null $v) {
            $this->title = $this->set('title', $v);
        }
    }

    #[Struct([
    ])]
    public ?int $userId = null
    {
        set (int|string|null $v) {
            $this->userId = $this->set('userId', $v);
        }
    }

    #[Struct([
    ])]
    public ?string $summary = null
    {
        set (string|null $v) {
            $this->summary = $this->set('summary', $v);
        }
    }

    #[Struct([
    ])]
    public ?string $createAt = null
    {
        set (string|null $v) {
            $this->createAt = $this->set('createAt', $v);
        }
    }

    #[Struct([
        self::CREATE_FILL => 0,
    ])]
    public ?int $deleteAt = null
    {
        set (int|string|null $v) {
            $this->deleteAt = $this->set('deleteAt', $v);
        }
    }

    protected function updateReal(): self
    {
        throw new \Exception('Update error');
    }
}
