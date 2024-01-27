<?php

declare(strict_types=1);

namespace Tests\Session;

use Leevel\Cache\Redis as CacheRedis;
use Leevel\Cache\Redis\PhpRedis;
use Leevel\Session\ISession;
use Leevel\Session\Redis;
use RedisException;
use Tests\TestCase;

final class RedisTest extends TestCase
{
    protected function setUp(): void
    {
        if (!\extension_loaded('redis')) {
            static::markTestSkipped('Redis extension must be loaded before use.');
        }

        try {
            $this->createRedisSessionHandler();
        } catch (RedisException) {
            static::markTestSkipped('Redis read error on connection and ignore.');
        }
    }

    public function testBaseUse(): void
    {
        $session = $this->createRedisSessionHandler();

        $this->assertInstanceof(ISession::class, $session);

        static::assertFalse($session->isStart());
        static::assertSame('', $session->getId());
        static::assertSame('UID', $session->getName());

        $session->start();
        static::assertTrue($session->isStart());

        $session->set('hello', 'world');
        static::assertSame(['hello' => 'world'], $session->all());
        static::assertTrue($session->has('hello'));
        static::assertSame('world', $session->get('hello'));

        $session->delete('hello');
        static::assertSame([], $session->all());
        static::assertFalse($session->has('hello'));
        static::assertNull($session->get('hello'));

        $session->start();
        static::assertTrue($session->isStart());
    }

    public function testSave(): void
    {
        $session = $this->createRedisSessionHandler();

        static::assertFalse($session->isStart());
        static::assertSame('', $session->getId());
        static::assertSame('UID', $session->getName());

        $session->start();
        static::assertTrue($session->isStart());

        $session->save();
        static::assertFalse($session->isStart());
    }

    public function testSaveAndStart(): void
    {
        $session = $this->createRedisSessionHandler();

        static::assertFalse($session->isStart());
        static::assertSame('', $session->getId());
        static::assertSame('UID', $session->getName());

        $session->start();
        static::assertTrue($session->isStart());
        static::assertSame([], $session->all());

        $session->set('foo', 'bar');
        $session->set('hello', 'world');
        static::assertSame(['foo' => 'bar', 'hello' => 'world'], $session->all());

        $session->save();
        static::assertFalse($session->isStart());

        $session->clear();
        static::assertSame([], $session->all());

        $session->set('other', 'value');
        static::assertSame(['other' => 'value'], $session->all());

        static::assertFalse($session->isStart());

        $sessionId = $session->getId();
        $session->start($sessionId);

        static::assertTrue($session->isStart());
        static::assertSame(['other' => 'value', 'foo' => 'bar', 'hello' => 'world', 'flash.old.key' => []], $session->all());

        $session->save();
        static::assertFalse($session->isStart());

        $sessionId = $session->getId();

        static::assertSame('a:4:{s:5:"other";s:5:"value";s:3:"foo";s:3:"bar";s:5:"hello";s:5:"world";s:13:"flash.old.key";a:0:{}}', $session->read($sessionId));

        $session->destroySession();

        static::assertSame('a:0:{}', $session->read($sessionId));
    }

    protected function createRedisSessionHandler(): Redis
    {
        $config = [
            'host' => $GLOBALS['LEEVEL_ENV']['SESSION']['REDIS']['HOST'],
            'port' => $GLOBALS['LEEVEL_ENV']['SESSION']['REDIS']['PORT'],
            'password' => $GLOBALS['LEEVEL_ENV']['SESSION']['REDIS']['PASSWORD'],
            'select' => 0,
            'timeout' => 0,
            'persistent' => false,
        ];

        return new Redis(new CacheRedis(new PhpRedis($config)));
    }
}
