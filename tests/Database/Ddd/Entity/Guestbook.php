<?php

declare(strict_types=1);

namespace Tests\Database\Ddd\Entity;

use Leevel\Database\Ddd\Entity;
use Leevel\Database\Ddd\Struct;

class Guestbook extends Entity
{
    public const TABLE = 'guest_book';

    public const ID = 'id';

    public const AUTO = 'id';

    public const REPOSITORY = GuestbookRepository::class;

    #[Struct([
        self::READONLY => true,
    ])]
    protected ?int $id = null;

    #[Struct([
    ])]
    protected ?string $name = null;

    #[Struct([
    ])]
    protected ?string $content = null;

    #[Struct([
    ])]
    protected ?string $createAt = null;
}
