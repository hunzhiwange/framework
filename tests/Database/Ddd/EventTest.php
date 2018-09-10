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
use Tests\Database\Ddd\Entity\TestEventEntity;
use Tests\TestCase;

/**
 * event test.
 *
 * @author Xiangmin Liu <635750556@qq.com>
 *
 * @since 2018.07.05
 *
 * @version 1.0
 */
class EventTest extends TestCase
{
    public function testBaseUse()
    {
        $entity = $this->makeEntity();

        $events = [
            'model_runEventSaveing',
            'model_runEventCreating',
            'model_runEventCreated',
            'model_runEventSaved',
        ];

        foreach ($events as $item) {
            if (isset($_SERVER[$item])) {
                unset($_SERVER[$item]);
            }
        }

        $entity->save()->flush();

        foreach ($events as $item) {
            $this->assertTrue($_SERVER[$item]);
        }

        foreach ($events as $item) {
            unset($_SERVER[$item]);
        }
    }

    public function testUpdateUse()
    {
        $entity = $this->makeEntity();

        $entity->id = 5;
        $entity->name = 'hello';

        $events = [
            'model_runEventSaveing',
            'model_runEventUpdating',
            'model_runEventUpdated',
            'model_runEventSaved',
        ];

        foreach ($events as $item) {
            if (isset($_SERVER[$item])) {
                unset($_SERVER[$item]);
            }
        }

        $entity->update()->flush();

        foreach ($events as $item) {
            $this->assertTrue($_SERVER[$item]);
        }

        foreach ($events as $item) {
            unset($_SERVER[$item]);
        }
    }

    protected function makeEntity()
    {
        $entity = new TestEventEntity();

        $this->assertInstanceof(Entity::class, $entity);

        return $entity;
    }
}
