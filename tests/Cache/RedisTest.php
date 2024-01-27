<?php

declare(strict_types=1);

namespace Tests\Cache;

use Leevel\Cache\Redis;
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
            $this->makeRedis();
        } catch (RedisException) {
            static::markTestSkipped('Redis read error on connection and ignore.');
        }
    }

    public function testBaseUse(): void
    {
        $redis = $this->makeRedis();

        $redis->set('hello', 'world');
        $redis->set('foo', 'bar');
        $redis->set('num', 123);

        static::assertSame('world', $redis->get('hello'));
        static::assertSame('bar', $redis->get('foo'));
        static::assertSame(123, $redis->get('num'));

        $redis->delete('hello');
        $redis->delete('foo');
        $redis->delete('num');

        static::assertFalse($redis->get('hello'));
        static::assertFalse($redis->get('foo'));
        static::assertFalse($redis->get('num'));

        $this->assertInstanceof(\Redis::class, $redis->getHandle());
        static::assertNull($redis->close());
        static::assertNull($redis->getHandle());
    }

    public function testSet(): void
    {
        $redis = $this->makeRedis();

        $redis->set('testset', 'world');

        static::assertSame('world', $redis->get('testset'));

        $redis->delete('testset');

        static::assertFalse($redis->get('hello'));

        $redis->set('testfoo', 'bar', 1);

        static::assertSame('bar', $redis->get('testfoo'));

        // 0.7 秒未过期
        usleep(700000);
        static::assertSame('bar', $redis->get('testfoo'));

        // 1.1 就过期
        usleep(400000);
        static::assertFalse($redis->get('testfoo'));
    }

    public function testIncrease(): void
    {
        $redis = $this->makeRedis();
        static::assertFalse($redis->get('testIncrease'));
        static::assertSame(1, $redis->increase('testIncrease'));
        static::assertSame(101, $redis->increase('testIncrease', 100));
        static::assertSame(201, $redis->increase('testIncrease', 100));
        $redis->delete('testIncrease');
        static::assertFalse($redis->get('testIncrease'));
    }

    public function testIncreaseWithExpire(): void
    {
        $redis = $this->makeRedis();
        static::assertFalse($redis->get('testIncreaseWithExpire'));
        static::assertSame(1, $redis->increase('testIncreaseWithExpire', 1, 1));
        static::assertSame(1, $redis->get('testIncreaseWithExpire'));

        // 0.7 秒未过期
        usleep(700000);
        static::assertSame(1, $redis->get('testIncreaseWithExpire'));

        // 1.1 就过期
        usleep(400000);
        static::assertFalse($redis->get('testIncreaseWithExpire'));
    }

    public function testIncreaseReturnFalse(): void
    {
        $redis = $this->makeRedis();
        static::assertFalse($redis->get('testIncreaseReturnFalse'));
        $redis->set('testIncreaseReturnFalse', 'world');
        static::assertFalse($redis->increase('testIncreaseReturnFalse'));
        $redis->delete('testIncreaseReturnFalse');
        static::assertFalse($redis->get('testIncreaseReturnFalse'));
    }

    public function testDecrease(): void
    {
        $redis = $this->makeRedis();
        static::assertFalse($redis->get('testDecrease'));
        static::assertSame(-1, $redis->decrease('testDecrease'));
        static::assertSame(-101, $redis->decrease('testDecrease', 100));
        static::assertSame(-201, $redis->decrease('testDecrease', 100));
        $redis->delete('testDecrease');
        static::assertFalse($redis->get('testIncrease'));
    }

    public function testDecreaseWithExpire(): void
    {
        $redis = $this->makeRedis();
        static::assertFalse($redis->get('testDecreaseWithExpire'));
        static::assertSame(-1, $redis->decrease('testDecreaseWithExpire', 1, 1));
        static::assertSame(-1, $redis->get('testDecreaseWithExpire'));

        // 0.7 秒未过期
        usleep(700000);
        static::assertSame(-1, $redis->get('testDecreaseWithExpire'));

        // 1.1 就过期
        usleep(400000);
        static::assertFalse($redis->get('testDecreaseWithExpire'));
    }

    public function testDecreaseReturnFalse(): void
    {
        $redis = $this->makeRedis();
        static::assertFalse($redis->get('testDecreaseReturnFalse'));
        $redis->set('testDecreaseReturnFalse', 'world');
        static::assertFalse($redis->decrease('testDecreaseReturnFalse'));
        $redis->delete('testDecreaseReturnFalse');
        static::assertFalse($redis->get('testDecreaseReturnFalse'));
    }

    public function testHas(): void
    {
        $redis = $this->makeRedis();

        static::assertFalse($redis->has('has'));
        $redis->set('has', 'world');
        static::assertTrue($redis->has('has'));
        $redis->delete('has');
    }

    public function testTtl(): void
    {
        $redis = $this->makeRedis();

        static::assertFalse($redis->has('ttl'));
        static::assertSame(-2, $redis->ttl('ttl'));
        $redis->set('ttl', 'world');
        static::assertSame(-1, $redis->ttl('ttl'));
        $redis->set('ttl', 'world', 1);
        static::assertSame(1, $redis->ttl('ttl'));
        $redis->set('ttl', 'world', 0);
        static::assertSame(-1, $redis->ttl('ttl'));
        $redis->delete('ttl');
    }

    public function testCall(): void
    {
        $redis = $this->makeRedis();

        static::assertSame([], $redis->keys('hello'));
        $redis->set('hello', 'world');
        static::assertSame(['hello'], $redis->keys('hello'));
        $redis->delete('hello');
    }

    public function testSelect(): void
    {
        $redis = $this->makeRedis([
            'select' => 1,
        ]);

        $redis->set('selecttest', 'world');

        static::assertSame('world', $redis->get('selecttest'));

        $redis->delete('selecttest');

        static::assertFalse($redis->get('selecttest'));
    }

    public function testWithPassword(): void
    {
        static::assertTrue(true);
        $this->expectException(\RedisException::class);

        $redis = $this->makeRedis([
            'password' => 'error password',
        ]);
    }

    protected function makeRedis(array $config = []): Redis
    {
        $default = [
            'host' => $GLOBALS['LEEVEL_ENV']['CACHE']['REDIS']['HOST'],
            'port' => $GLOBALS['LEEVEL_ENV']['CACHE']['REDIS']['PORT'],
            'password' => $GLOBALS['LEEVEL_ENV']['CACHE']['REDIS']['PASSWORD'],
            'select' => 0,
            'timeout' => 0,
            'persistent' => false,
            'expire' => 0,
        ];

        $config = array_merge($default, $config);

        return new Redis($config);
    }
}
