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
 * (c) 2010-2019 http://queryphp.com All rights reserved.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tests\Database\Ddd\Entity\Relation;

use Leevel\Database\Ddd\Entity;

/**
 * postContent.
 *
 * @author Xiangmin Liu <635750556@qq.com>
 *
 * @since 2018.10.13
 *
 * @version 1.0
 */
class PostContent extends Entity
{
    const TABLE = 'post_content';

    const ID = null;

    const AUTO = null;

    const STRUCT = [
        'post_id' => [
            'readonly' => true,
        ],
        'content' => [],
    ];

    private $postId;

    private $content;

    public function setter(string $prop, $value): Entity
    {
        $this->{$this->prop($prop)} = $value;

        return $this;
    }

    public function getter(string $prop)
    {
        return $this->{$this->prop($prop)};
    }
}
