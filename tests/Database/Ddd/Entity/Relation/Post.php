<?php

declare(strict_types=1);

namespace Tests\Database\Ddd\Entity\Relation;

use Leevel\Database\Ddd\Entity;
use Leevel\Database\Ddd\Relation\Relation;
use Leevel\Database\Ddd\Struct;

class Post extends Entity
{
    public const TABLE = 'post';

    public const ID = 'id';

    public const AUTO = 'id';

    public const STRUCT = [
        'id' => [
            self::READONLY => true,
        ],
        'title' => [],
        'user_id' => [],
        'summary' => [],
        'create_at' => [],
        'delete_at' => [
            self::CREATE_FILL => 0,
        ],
        'user' => [
            self::BELONGS_TO => User::class,
            self::SOURCE_KEY => 'user_id',
            self::TARGET_KEY => 'id',
        ],
        'comment' => [
            self::HAS_MANY => Comment::class,
            self::SOURCE_KEY => 'id',
            self::TARGET_KEY => 'post_id',
            self::RELATION_SCOPE => 'comment',
        ],
        'post_content' => [
            self::HAS_ONE => PostContent::class,
            self::SOURCE_KEY => 'id',
            self::TARGET_KEY => 'post_id',
        ],
        'user_not_defined_source_key' => [
            self::BELONGS_TO => User::class,
            self::TARGET_KEY => 'id',
        ],
        'user_not_defined_target_key' => [
            self::BELONGS_TO => User::class,
            self::SOURCE_KEY => 'id',
        ],
        'comment_not_defined_source_key' => [
            self::HAS_MANY => Comment::class,
            self::TARGET_KEY => 'post_id',
            self::RELATION_SCOPE => 'comment',
        ],
        'comment_not_defined_target_key' => [
            self::HAS_MANY => Comment::class,
            self::SOURCE_KEY => 'id',
            self::RELATION_SCOPE => 'comment',
        ],
        'post_content_not_defined_source_key' => [
            self::HAS_ONE => PostContent::class,
            self::TARGET_KEY => 'post_id',
        ],
        'post_content_not_defined_target_key' => [
            self::HAS_ONE => PostContent::class,
            self::SOURCE_KEY => 'id',
        ],
    ];

    public const DELETE_AT = 'delete_at';

    #[Struct([
        self::READONLY => true,
    ])]
    protected ?int $id = null;

    #[Struct([
    ])]
    protected ?string $title = null;

    #[Struct([
    ])]
    protected ?int $userId = null;

    #[Struct([
    ])]
    protected ?string $summary = null;

    #[Struct([
    ])]
    protected ?string $createAt = null;

    #[Struct([
        self::CREATE_FILL => 0,
    ])]
    protected ?int $deleteAt = null;

    #[Struct([
        self::BELONGS_TO => User::class,
        self::SOURCE_KEY => 'user_id',
        self::TARGET_KEY => 'id',
    ])]
    protected ?int $user = null;

    #[Struct([
        self::HAS_MANY => Comment::class,
        self::SOURCE_KEY => 'id',
        self::TARGET_KEY => 'post_id',
        self::RELATION_SCOPE => 'comment',
    ])]
    protected ?int $comment = null;

    #[Struct([
        self::HAS_ONE => PostContent::class,
        self::SOURCE_KEY => 'id',
        self::TARGET_KEY => 'post_id',
    ])]
    protected ?int $postContent = null;

    #[Struct([
        self::BELONGS_TO => User::class,
        self::TARGET_KEY => 'id',
    ])]
    protected ?int $userNotDefinedSourceKey = null;

    #[Struct([
        self::BELONGS_TO => User::class,
        self::SOURCE_KEY => 'id',
    ])]
    protected ?int $userNotDefinedTargetKey = null;

    #[Struct([
        self::HAS_MANY => Comment::class,
        self::TARGET_KEY => 'post_id',
        self::RELATION_SCOPE => 'comment',
    ])]
    protected ?int $comment_not_defined_source_key = null;

    #[Struct([
        self::HAS_MANY => Comment::class,
        self::SOURCE_KEY => 'id',
        self::RELATION_SCOPE => 'comment',
    ])]
    protected ?int $comment_not_defined_target_key = null;

    #[Struct([
        self::HAS_ONE => PostContent::class,
        self::TARGET_KEY => 'post_id',
    ])]
    protected ?int $postContentNotDefinedSourceKey = null;

    #[Struct([
        self::HAS_ONE => PostContent::class,
        self::SOURCE_KEY => 'id',
    ])]
    protected ?int $postContentNotDefinedTargetKey = null;

    protected function relationScopeComment(Relation $relation): void
    {
        $relation->where('id', '>', 4);
    }
}
