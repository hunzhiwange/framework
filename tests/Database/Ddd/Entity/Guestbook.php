<?php

declare(strict_types=1);

namespace Tests\Database\Ddd\Entity;

use Leevel\Database\Ddd\Entity;
use Leevel\Database\Ddd\Struct;

class Guestbook extends Entity
{
    public const string TABLE = 'guest_book';

    public const string ID = 'id';

    public const string AUTO = 'id';

    public const string REPOSITORY = GuestbookRepository::class;

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
    public ?string $name = null
    {
        set (string|null $v) {
            $this->name = $this->set('name', $v);
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

    #[Struct([
    ])]
    public ?string $createAt = null
    {
        set (string|null $v) {
            $this->createAt = $this->set('createAt', $v);
        }
    }
}
