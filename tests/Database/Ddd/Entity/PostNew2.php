<?php

declare(strict_types=1);

namespace Tests\Database\Ddd\Entity;

use Leevel\Database\Ddd\Entity;
use Leevel\Database\Ddd\Struct;

class PostNew2 extends Entity
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
        set (int|string|null $v) {
            $this->id = $this->set('id', $v);
        }
    }

    #[Struct([
        self::COLUMN_NAME => '标题',
        self::COLUMN_STRUCT => [
            'type' => 'varchar',
            'default' => '',
            'length' => 64,
            'format' => [self::class, 'titleFormat'],
        ],
    ])]
    public ?string $title = null
    {
        set (string|null $v) {
            $this->title = $this->set('title', $v);
        }
    }

    #[Struct([
        self::COLUMN_NAME => '用户ID',
        self::COLUMN_STRUCT => [
            'type' => 'bigint',
            'default' => 0,
            'format' => null,
        ],
    ])]
    public ?int $userId = null
    {
        set (int|string|null $v) {
            $this->userId = $this->set('userId', $v);
        }
    }

    #[Struct([
        self::COLUMN_NAME => '文章摘要',
        self::COLUMN_STRUCT => [
            'type' => 'varchar',
            'default' => '',
            'length' => 200,
            'format' => 'not_found',
            'format_raw' => true,
        ],
    ])]
    public ?string $summary = null
    {
        set (string|null $v) {
            $this->summary = $this->set('summary', $v);
        }
    }

    #[Struct([
        self::COLUMN_NAME => '创建时间',
        self::COLUMN_STRUCT => [
            'type' => 'datetime',
            'default' => 'CURRENT_TIMESTAMP',
        ],
    ])]
    public ?string $createAt = null
    {
        set (string|null $v) {
            $this->createAt = $this->set('createAt', $v);
        }
    }

    #[Struct([
        self::CREATE_FILL => 0,
        self::COLUMN_NAME => '删除时间',
        self::COLUMN_STRUCT => [
            'type' => 'bigint',
            'default' => 0,
        ],
    ])]
    public ?int $deleteAt = null
    {
        set (int|string|null $v) {
            $this->deleteAt = $this->set('deleteAt', $v);
        }
    }

    public static function titleFormat(string $v): string
    {
        return $v.'_new';
    }
}
