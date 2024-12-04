<?php

declare(strict_types=1);

namespace Tests\Database\Ddd\Entity;

use Leevel\Database\Ddd\Entity;
use Leevel\Database\Ddd\Struct;

class PostNew8 extends Entity
{
    public const string TABLE = 'post';

    public const string ID = 'id';

    public const string AUTO = 'id';

    public const string DELETE_AT = 'delete_at';

    #[Struct([
        self::READONLY => true,
        self::COLUMN_NAME => 'ID',
        self::COLUMN_STRUCT => [
            'type' => 'bigint',
        ],
    ])]
    public ?int $id = null
    {
        set (int|null $v) {
            $this->id = $this->set('id', $v);
        }
    }

    #[Struct([
        self::COLUMN_NAME => '用户ID',
        self::COLUMN_STRUCT => [
            'type' => 'float',
            'default' => 0,
        ],
    ])]
    public ?float $userId = null
    {
        set (float|null $v) {
            $this->userId = $this->set('userId', $v);
        }
    }

    protected function userIdTransformValue(mixed $value): float
    {
        return (float) $value + 5;
    }
}
