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

namespace Tests\Database\Ddd;

use Leevel\Database\Ddd\Entity;
use Leevel\Database\Ddd\IEntity;
use Leevel\Di\Container;
use Leevel\Event\Dispatch;
use Tests\Database\DatabaseTestCase as TestCase;
use Tests\Database\Ddd\Entity\TestEventEntity;

/**
 * event test.
 *
 * @author Xiangmin Liu <635750556@qq.com>
 *
 * @since 2018.11.06
 *
 * @version 1.0
 */
class EntityEventTest extends TestCase
{
    protected function tearDown(): void
    {
        Entity::withEventDispatch(null);
    }

    public function testBaseUse(): void
    {
        $dispatch = new Dispatch(new Container());
        $this->assertNull(Entity::eventDispatch());
        Entity::withEventDispatch($dispatch);
        $this->assertInstanceof(Dispatch::class, Entity::eventDispatch());

        $test = new TestEventEntity(['name' => 'foo']);
        TestEventEntity::event(IEntity::BEFORE_CREATE_EVENT, function () {
            $_SERVER['ENTITY.BEFORE_CREATE_EVENT'] = 'BEFORE_CREATE_EVENT';
        });
        TestEventEntity::event(IEntity::AFTER_CREATE_EVENT, function () {
            $_SERVER['ENTITY.AFTER_CREATE_EVENT'] = 'AFTER_CREATE_EVENT';
        });

        $this->assertFalse(isset($_SERVER['ENTITY.BEFORE_CREATE_EVENT']));
        $this->assertFalse(isset($_SERVER['ENTITY.AFTER_CREATE_EVENT']));

        $test->create()->flush();

        $this->assertTrue(isset($_SERVER['ENTITY.BEFORE_CREATE_EVENT']));
        $this->assertTrue(isset($_SERVER['ENTITY.AFTER_CREATE_EVENT']));
        $this->assertSame('BEFORE_CREATE_EVENT', $_SERVER['ENTITY.BEFORE_CREATE_EVENT']);
        $this->assertSame('AFTER_CREATE_EVENT', $_SERVER['ENTITY.AFTER_CREATE_EVENT']);

        unset($_SERVER['ENTITY.BEFORE_CREATE_EVENT'], $_SERVER['ENTITY.AFTER_CREATE_EVENT']);
    }

    public function testEventDispatchWasNotSet(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Event dispatch was not set.');

        TestEventEntity::event(IEntity::BEFORE_CREATE_EVENT, function () {
        });
    }

    /**
     * @dataProvider getSupportedEvent
     *
     * @param string $event
     */
    public function testSupportEvent($event): void
    {
        $dispatch = new Dispatch(new Container());
        $this->assertNull(Entity::eventDispatch());
        Entity::withEventDispatch($dispatch);
        $this->assertInstanceof(Dispatch::class, Entity::eventDispatch());

        $supportEvent = TestEventEntity::supportEvent();
        $this->assertTrue(in_array($event, $supportEvent, true));
    }

    public function getSupportedEvent()
    {
        return [
            [IEntity::BEFORE_SAVE_EVENT],
            [IEntity::AFTER_SAVE_EVENT],
            [IEntity::BEFORE_CREATE_EVENT],
            [IEntity::AFTER_CREATE_EVENT],
            [IEntity::BEFORE_UPDATE_EVENT],
            [IEntity::AFTER_UPDATE_EVENT],
            [IEntity::BEFORE_DELETE_EVENT],
            [IEntity::AFTER_DELETE_EVENT],
            [IEntity::BEFORE_SOFT_DELETE_EVENT],
            [IEntity::AFTER_SOFT_DELETE_EVENT],
            [IEntity::BEFORE_SOFT_RESTORE_EVENT],
            [IEntity::AFTER_SOFT_RESTORE_EVENT],
        ];
    }

    public function testEventWasNotSupport(): void
    {
        $dispatch = new Dispatch(new Container());
        $this->assertNull(Entity::eventDispatch());
        Entity::withEventDispatch($dispatch);
        $this->assertInstanceof(Dispatch::class, Entity::eventDispatch());

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Event `not_support` do not support.');

        TestEventEntity::event('not_support', function () {
        });
    }

    protected function getDatabaseTable(): array
    {
        return ['test'];
    }
}
