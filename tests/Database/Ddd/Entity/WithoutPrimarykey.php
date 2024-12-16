<?php

declare(strict_types=1);

namespace Tests\Database\Ddd\Entity;

use Leevel\Database\Ddd\Entity;
use Leevel\Database\Ddd\Struct;

class WithoutPrimarykey extends Entity
{
    public const string TABLE = 'without_primarykey';

    public const string ID = 'goods_id';

    public const ?string AUTO = null;

    #[Struct([
        self::READONLY => true,
    ])]
    public ?int $goodsId = null
    {
        set (int|string|null $v) {
            $this->goodsId = $this->set('goodsId', $v);
        }
    }

    #[Struct([
    ])]
    public ?string $description = null
    {
        set (string|null $v) {
            $this->description = $this->set('description', $v);
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
}
