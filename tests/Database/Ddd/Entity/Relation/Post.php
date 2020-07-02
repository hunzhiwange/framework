<?php

declare(strict_types=1);

/*
 * This file is part of the ************************ package.
 * _____________                           _______________
 *  ______/     \__  _____  ____  ______  / /_  _________
 *   ____/ __   / / / / _ \/ __`\/ / __ \/ __ \/ __ \___
 *    __/ / /  / /_/ /  __/ /  \  / /_/ / / / / /_/ /__
 *      \_\ \_/\____/\___/_/   / / .___/_/ /_/ .___/
 *         \_\                /_/_/         /_/
 *
 * The PHP Framework For Code Poem As Free As Wind. <Query Yet Simple>
 * (c) 2010-2020 http://queryphp.com All rights reserved.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tests\Database\Ddd\Entity\Relation;

use Leevel\Database\Ddd\Entity;
use Leevel\Database\Ddd\GetterSetter;
use Leevel\Database\Ddd\Relation\Relation;

class Post extends Entity
{
    use GetterSetter;

    const TABLE = 'post';

    const ID = 'id';

    const AUTO = 'id';

    const STRUCT = [
        'id' => [
            self::READONLY           => true,
        ],
        'title'     => [],
        'user_id'   => [],
        'summary'   => [],
        'create_at' => [],
        'delete_at' => [
            self::CREATE_FILL => 0,
        ],
        'user'      => [
            self::BELONGS_TO     => User::class,
            self::SOURCE_KEY     => 'user_id',
            self::TARGET_KEY     => 'id',
        ],
        'comment' => [
            self::HAS_MANY          => Comment::class,
            self::SOURCE_KEY        => 'id',
            self::TARGET_KEY        => 'post_id',
            self::RELATION_SCOPE    => 'comment',
        ],
        'post_content' => [
            self::HAS_ONE     => PostContent::class,
            self::SOURCE_KEY  => 'id',
            self::TARGET_KEY  => 'post_id',
        ],
        'user_not_defined_source_key'      => [
            self::BELONGS_TO     => User::class,
            self::TARGET_KEY     => 'id',
        ],
        'user_not_defined_target_key'      => [
            self::BELONGS_TO     => User::class,
            self::SOURCE_KEY     => 'id',
        ],
        'comment_not_defined_source_key' => [
            self::HAS_MANY          => Comment::class,
            self::TARGET_KEY        => 'post_id',
            self::RELATION_SCOPE    => 'comment',
        ],
        'comment_not_defined_target_key' => [
            self::HAS_MANY          => Comment::class,
            self::SOURCE_KEY        => 'id',
            self::RELATION_SCOPE    => 'comment',
        ],
        'post_content_not_defined_source_key' => [
            self::HAS_ONE     => PostContent::class,
            self::TARGET_KEY  => 'post_id',
        ],
        'post_content_not_defined_target_key' => [
            self::HAS_ONE     => PostContent::class,
            self::SOURCE_KEY  => 'id',
        ],
    ];

    const DELETE_AT = 'delete_at';

    protected function relationScopeComment(Relation $relation): void
    {
        $relation->where('id', '>', 4);
    }
}
