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

namespace Tests\Database\Ddd;

use Leevel\Database\Ddd\Entity;
use Leevel\Di\Container;
use Leevel\Event\Dispatch;
use Tests\Database\DatabaseTestCase as TestCase;
use Tests\Database\Ddd\Entity\TestEventEntity;

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
        TestEventEntity::event(Entity::BEFORE_CREATE_EVENT, function () {
            $_SERVER['ENTITY.BEFORE_CREATE_EVENT'] = 'BEFORE_CREATE_EVENT';
        });
        TestEventEntity::event(Entity::AFTER_CREATE_EVENT, function () {
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

        TestEventEntity::event(Entity::BEFORE_CREATE_EVENT, function () {
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
            [Entity::BEFORE_SAVE_EVENT],
            [Entity::AFTER_SAVE_EVENT],
            [Entity::BEFORE_CREATE_EVENT],
            [Entity::AFTER_CREATE_EVENT],
            [Entity::BEFORE_UPDATE_EVENT],
            [Entity::AFTER_UPDATE_EVENT],
            [Entity::BEFORE_DELETE_EVENT],
            [Entity::AFTER_DELETE_EVENT],
            [Entity::BEFORE_SOFT_DELETE_EVENT],
            [Entity::AFTER_SOFT_DELETE_EVENT],
            [Entity::BEFORE_SOFT_RESTORE_EVENT],
            [Entity::AFTER_SOFT_RESTORE_EVENT],
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
