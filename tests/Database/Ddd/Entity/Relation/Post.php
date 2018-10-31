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
 * (c) 2010-2018 http://queryphp.com All rights reserved.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tests\Database\Ddd\Entity\Relation;

use Leevel\Database\Ddd\Entity;

/**
 * post.
 *
 * @author Xiangmin Liu <635750556@qq.com>
 *
 * @since 2018.10.13
 *
 * @version 1.0
 */
class Post extends Entity
{
    const TABLE = 'post';

    const ID = 'id';

    const AUTO = 'id';

    const STRUCT = [
        'id' => [
            'readonly'           => true,
        ],
        'title'     => [],
        'user_id'   => [],
        'summary'   => [],
        'create_at' => [],
        'delete_at' => [],
        'user'      => [
            self::BELONGS_TO => User::class,
            'source_key'     => 'user_id',
            'target_key'     => 'id',
        ],
        'comment' => [
            self::HAS_MANY => Comment::class,
            'source_key'   => 'id',
            'target_key'   => 'post_id',
            self::SCOPE    => 'comment',
        ],
        'post_content' => [
            self::HAS_ONE => PostContent::class,
            'source_key'  => 'id',
            'target_key'  => 'post_id',
        ],
    ];

    const DELETE_AT = 'delete_at';

    private $id;

    private $title;

    private $userId;

    private $summary;

    private $createAt;

    private $deleteAt;

    private $user;

    private $comment;

    private $postContent;

    public function setter(string $prop, $value)
    {
        $this->{$this->prop($prop)} = $value;

        return $this;
    }

    public function getter(string $prop)
    {
        return $this->{$this->prop($prop)};
    }

    public function scopeComment($select)
    {
        $select->where('id', '>', 4);
    }

    public function scopeTest($select)
    {
        $select->where('id', '>', 4);
    }

    public function scopeTest2($select)
    {
        $select->where('id', '<', 10);
    }

    public function scopeTest3($select)
    {
        $select->where('id', 5);
    }
}
