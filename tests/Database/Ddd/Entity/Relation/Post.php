<?php

declare(strict_types=1);

namespace Tests\Database\Ddd\Entity\Relation;

use Leevel\Database\Ddd\Entity;
use Leevel\Database\Ddd\EntityCollection as Collection;
use Leevel\Database\Ddd\Relation\Relation;
use Leevel\Database\Ddd\Struct;

class Post extends Entity
{
    public const string TABLE = 'post';

    public const string ID = 'id';

    public const string AUTO = 'id';

    public const string DELETE_AT = 'delete_at';

    #[Struct([
        self::READONLY => true,
        self::COLUMN_NAME => 'ID',
    ])]
    public ?int $id = null
    {
        set (int|null $v) {
            $this->id = $this->set('id', $v);
        }
    }

    #[Struct([
        self::COLUMN_NAME => '标题',
    ])]
    public ?string $title = null
    {
        set (string|null $v) {
            $this->title = $this->set('title', $v);
        }
    }

    #[Struct([
        self::COLUMN_NAME => '用户ID',
    ])]
    public ?int $userId = null
    {
        set (int|null $v) {
            $this->userId = $this->set('userId', $v);
        }
    }

    #[Struct([
        self::COLUMN_NAME => '文章摘要',
    ])]
    public ?string $summary = null
    {
        set (string|null $v) {
            $this->summary = $this->set('summary', $v);
        }
    }

    #[Struct([
        self::COLUMN_NAME => '创建时间',
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
    ])]
    public ?int $deleteAt = null
    {
        set (int|null $v) {
            $this->deleteAt = $this->set('deleteAt', $v);
        }
    }

    #[Struct([
        self::BELONGS_TO => User::class,
        self::SOURCE_KEY => 'user_id',
        self::TARGET_KEY => 'id',
    ])]
    public ?User $user = null
    {
        set (User|null $v) {
            $this->user = $this->set('user', $v);
        }
    }

    #[Struct([
        self::HAS_MANY => Comment::class,
        self::SOURCE_KEY => 'id',
        self::TARGET_KEY => 'post_id',
        self::RELATION_SCOPE => 'comment',
    ])]
    public ?Collection $comment = null
    {
        set (Collection|null $v) {
            $this->comment = $this->set('comment', $v);
        }
    }

    #[Struct([
        self::HAS_ONE => PostContent::class,
        self::SOURCE_KEY => 'id',
        self::TARGET_KEY => 'post_id',
    ])]
    public ?PostContent $postContent = null
    {
        set (PostContent|null $v) {
            $this->postContent = $this->set('postContent', $v);
        }
    }

    #[Struct([
        self::BELONGS_TO => User::class,
        self::TARGET_KEY => 'id',
    ])]
    public ?int $userNotDefinedSourceKey = null
    {
        set (int|null $v) {
            $this->userNotDefinedSourceKey = $this->set('userNotDefinedSourceKey', $v);
        }
    }

    #[Struct([
        self::BELONGS_TO => User::class,
        self::SOURCE_KEY => 'id',
    ])]
    public ?int $userNotDefinedTargetKey = null
    {
        set (int|null $v) {
            $this->userNotDefinedTargetKey = $this->set('userNotDefinedTargetKey', $v);
        }
    }

    #[Struct([
        self::HAS_MANY => Comment::class,
        self::TARGET_KEY => 'post_id',
        self::RELATION_SCOPE => 'comment',
    ])]
    public ?int $commentNotDefinedSourceKey = null
    {
        set (int|null $v) {
            $this->commentNotDefinedSourceKey = $this->set('commentNotDefinedSourceKey', $v);
        }
    }

    #[Struct([
        self::HAS_MANY => Comment::class,
        self::SOURCE_KEY => 'id',
        self::RELATION_SCOPE => 'comment',
    ])]
    public ?int $commentNotDefinedTargetKey = null
    {
        set (int|null $v) {
            $this->commentNotDefinedTargetKey = $this->set('commentNotDefinedTargetKey', $v);
        }
    }

    #[Struct([
        self::HAS_ONE => PostContent::class,
        self::TARGET_KEY => 'post_id',
    ])]
    public ?int $postContentNotDefinedSourceKey = null
    {
        set (int|null $v) {
            $this->postContentNotDefinedSourceKey = $this->set('postContentNotDefinedSourceKey', $v);
        }
    }

    #[Struct([
        self::HAS_ONE => PostContent::class,
        self::SOURCE_KEY => 'id',
    ])]
    public ?int $postContentNotDefinedTargetKey = null
    {
        set (int|null $v) {
            $this->postContentNotDefinedTargetKey = $this->set('postContentNotDefinedTargetKey', $v);
        }
    }

    protected function relationScopeComment(Relation $relation): void
    {
        $relation->where('id', '>', 4);
    }

    protected function relationScopeStringUserRelationScope(Relation $relation): void
    {
        $relation->where('id', '>', 4);
    }
}
