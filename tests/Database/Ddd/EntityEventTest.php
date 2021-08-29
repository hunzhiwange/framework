<?php

declare(strict_types=1);

namespace Tests\Database\Ddd;

use Leevel\Database\Ddd\Entity;
use Leevel\Di\Container;
use Leevel\Event\Dispatch;
use Tests\Database\DatabaseTestCase as TestCase;
use Tests\Database\Ddd\Entity\DemoEventEntity;

/**
 * @api(
 *     zh-CN:title="实体事件",
 *     path="orm/event",
 *     zh-CN:description="
 * 实体在新增和更新时，预植了事件监听器，可以定义一些事件。
 * ",
 * )
 */
class EntityEventTest extends TestCase
{
    protected function tearDown(): void
    {
        Entity::withEventDispatch(null);
    }

    /**
     * @api(
     *     zh-CN:title="事件基本使用方法",
     *     zh-CN:description="",
     *     zh-CN:note="",
     * )
     */
    public function testBaseUse(): void
    {
        $dispatch = new Dispatch(new Container());
        $this->assertNull(Entity::eventDispatch());
        Entity::withEventDispatch($dispatch);
        $this->assertInstanceof(Dispatch::class, Entity::eventDispatch());

        $test = new DemoEventEntity(['name' => 'foo']);
        DemoEventEntity::event(Entity::BEFORE_CREATING_EVENT, function () {
            $_SERVER['ENTITY.BEFORE_CREATING_EVENT'] = 'BEFORE_CREATING_EVENT';
        });
        DemoEventEntity::event(Entity::AFTER_CREATED_EVENT, function () {
            $_SERVER['ENTITY.AFTER_CREATED_EVENT'] = 'AFTER_CREATED_EVENT';
        });

        $this->assertFalse(isset($_SERVER['ENTITY.BEFORE_CREATING_EVENT']));
        $this->assertFalse(isset($_SERVER['ENTITY.AFTER_CREATED_EVENT']));

        $test->create()->flush();

        $this->assertTrue(isset($_SERVER['ENTITY.BEFORE_CREATING_EVENT']));
        $this->assertTrue(isset($_SERVER['ENTITY.AFTER_CREATED_EVENT']));
        $this->assertSame('BEFORE_CREATING_EVENT', $_SERVER['ENTITY.BEFORE_CREATING_EVENT']);
        $this->assertSame('AFTER_CREATED_EVENT', $_SERVER['ENTITY.AFTER_CREATED_EVENT']);

        unset($_SERVER['ENTITY.BEFORE_CREATING_EVENT'], $_SERVER['ENTITY.AFTER_CREATED_EVENT']);
    }

    public function testEventDispatchWasNotSet(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Event dispatch was not set.');

        DemoEventEntity::event(Entity::BEFORE_CREATING_EVENT, function () {
        });
    }

    /**
     * @api(
     *     zh-CN:title="实体支持的事件",
     *     zh-CN:description="
     * ``` php
     * {[\Leevel\Kernel\Utils\Doc::getMethodBody(\Tests\Database\Ddd\EntityEventTest::class, 'getSupportedEvent')]}
     * ```
     * ",
     *     zh-CN:note="",
     * )
     *
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

        $supportEvent = DemoEventEntity::supportEvent();
        $this->assertTrue(in_array($event, $supportEvent, true));
    }

    public function getSupportedEvent()
    {
        return [
            [Entity::BEFORE_SAVEING_EVENT],
            [Entity::AFTER_SAVED_EVENT],
            [Entity::BEFORE_CREATING_EVENT],
            [Entity::AFTER_CREATED_EVENT],
            [Entity::BEFORE_UPDATING_EVENT],
            [Entity::AFTER_UPDATED_EVENT],
            [Entity::BEFORE_DELETING_EVENT],
            [Entity::AFTER_DELETED_EVENT],
            [Entity::BEFORE_SOFT_DELETING_EVENT],
            [Entity::AFTER_SOFT_DELETED_EVENT],
            [Entity::BEFORE_SOFT_RESTORING_EVENT],
            [Entity::AFTER_SOFT_RESTORED_EVENT],
        ];
    }

    /**
     * @api(
     *     zh-CN:title="不受支持的事件",
     *     zh-CN:description="",
     *     zh-CN:note="",
     * )
     */
    public function testEventWasNotSupport(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Event `not_support` do not support.');

        $dispatch = new Dispatch(new Container());
        $this->assertNull(Entity::eventDispatch());
        Entity::withEventDispatch($dispatch);
        $this->assertInstanceof(Dispatch::class, Entity::eventDispatch());
        DemoEventEntity::event('not_support', function () {
        });
    }

    protected function getDatabaseTable(): array
    {
        return ['test'];
    }
}
