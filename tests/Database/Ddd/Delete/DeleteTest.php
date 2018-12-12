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

namespace Tests\Database\Ddd\Delete;

use Leevel\Database\Ddd\Entity;
use Tests\Database\DatabaseTestCase as TestCase;
use Tests\Database\Ddd\Entity\TestEntity;

/**
 * delete test.
 *
 * @author Xiangmin Liu <635750556@qq.com>
 *
 * @since 2018.10.23
 *
 * @version 1.0
 */
class DeleteTest extends TestCase
{
    public function testBaseUse()
    {
        $entity = new TestEntity(['id' => 5, 'name' => 'foo']);

        $this->assertInstanceof(Entity::class, $entity);

        $this->assertSame('foo', $entity->name);
        $this->assertSame(['id', 'name'], $entity->changed());

        $this->assertNull($entity->flushData());

        $entity->destroy();

        $data = <<<'eot'
[
    {
        "id": 5
    }
]
eot;

        $this->assertSame(
            $data,
            $this->varJson(
                $entity->flushData()
            )
        );
    }
}
