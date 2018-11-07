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

namespace Tests\Database\Ddd;

use Leevel\Database\Ddd\Entity;
use Tests\Database\Ddd\Entity\Relation\Post;
use Tests\TestCase;

/**
 * entity test.
 *
 * @author Xiangmin Liu <635750556@qq.com>
 *
 * @since 2018.11.02
 *
 * @version 1.0
 */
class EntityTest extends TestCase
{
    public function testPropNotDefined()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage(
            'Prop `name` of entity `Tests\\Database\\Ddd\\TestPropErrorEntity` was not defined.'
        );

        $entity = new TestPropErrorEntity();
        $entity->name = 5;
    }

    public function testSetPropManyTimesDoNothing()
    {
        $entity = new Post();
        $entity->title = 5;
        $entity->title = 5;
        $entity->title = 5;
        $entity->title = 5;

        $this->assertSame(5, $entity->title);
    }

    public function testSetPropButIsRelation()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage(
            'Cannot set a relation prop `post_content` on entity `Tests\\Database\\Ddd\\Entity\\Relation\\Post`.'
        );

        $entity = new Post();
        $entity->postContent = 5;
    }

    public function testDatabaseResolverWasNotSet()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage(
            'Database resolver was not set.'
        );

        $entity = new Post(['title' => 'foo']);
        $entity->create()->flush();
    }
}

class TestPropErrorEntity extends Entity
{
    const TABLE = 'error';

    const ID = 'id';

    const AUTO = 'id';

    const STRUCT = [
        'id' => [
            'readonly'           => true,
        ],
        'title'     => [],
        'name'      => [],
    ];

    private $id;

    private $title;

    public function setter(string $prop, $value)
    {
        $this->{$this->prop($prop)} = $value;

        return $this;
    }

    public function getter(string $prop)
    {
        return $this->{$this->prop($prop)};
    }
}
