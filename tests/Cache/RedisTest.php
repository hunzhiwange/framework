<?php

declare(strict_types=1);

namespace Tests\Cache;

use Leevel\Cache\Redis;
use Tests\TestCase;

final class RedisTest extends TestCase
{
    #[\Override]
    protected function setUp(): void
    {
        if (!\extension_loaded('redis')) {
            self::markTestSkipped('Redis extension must be loaded before use.');
        }

        try {
            $this->makeRedis();
        } catch (\RedisException) {
            self::markTestSkipped('Redis read error on connection and ignore.');
        }
    }

    public function testBaseUse(): void
    {
        $redis = $this->makeRedis();

        $redis->set('hello', 'world');
        $redis->set('foo', 'bar');
        $redis->set('num', 123);

        self::assertSame('world', $redis->get('hello'));
        self::assertSame('bar', $redis->get('foo'));
        self::assertSame(123, $redis->get('num'));

        $redis->delete('hello');
        $redis->delete('foo');
        $redis->delete('num');

        self::assertFalse($redis->get('hello'));
        self::assertFalse($redis->get('foo'));
        self::assertFalse($redis->get('num'));

        $this->assertInstanceof(\Redis::class, $redis->getHandle());
        self::assertNull($redis->close());
        self::assertNull($redis->getHandle());
    }

    public function testSet(): void
    {
        $redis = $this->makeRedis();

        $redis->set('testset', 'world');

        self::assertSame('world', $redis->get('testset'));

        $redis->delete('testset');

        self::assertFalse($redis->get('hello'));

        $redis->set('testfoo', 'bar', 1);

        self::assertSame('bar', $redis->get('testfoo'));

        // 0.7 秒未过期
        usleep(700000);
        self::assertSame('bar', $redis->get('testfoo'));

        // 1.1 就过期
        usleep(400000);
        self::assertFalse($redis->get('testfoo'));
    }

    public function testIncrease(): void
    {
        $redis = $this->makeRedis();
        self::assertFalse($redis->get('testIncrease'));
        self::assertSame(1, $redis->increase('testIncrease'));
        self::assertSame(101, $redis->increase('testIncrease', 100));
        self::assertSame(201, $redis->increase('testIncrease', 100));
        $redis->delete('testIncrease');
        self::assertFalse($redis->get('testIncrease'));
    }

    public function testIncreaseWithExpire(): void
    {
        $redis = $this->makeRedis();
        self::assertFalse($redis->get('testIncreaseWithExpire'));
        self::assertSame(1, $redis->increase('testIncreaseWithExpire', 1, 1));
        self::assertSame(1, $redis->get('testIncreaseWithExpire'));

        // 0.7 秒未过期
        usleep(700000);
        self::assertSame(1, $redis->get('testIncreaseWithExpire'));

        // 1.1 就过期
        usleep(400000);
        self::assertFalse($redis->get('testIncreaseWithExpire'));
    }

    public function testIncreaseReturnFalse(): void
    {
        $redis = $this->makeRedis();
        self::assertFalse($redis->get('testIncreaseReturnFalse'));
        $redis->set('testIncreaseReturnFalse', 'world');
        self::assertFalse($redis->increase('testIncreaseReturnFalse'));
        $redis->delete('testIncreaseReturnFalse');
        self::assertFalse($redis->get('testIncreaseReturnFalse'));
    }

    public function testDecrease(): void
    {
        $redis = $this->makeRedis();
        self::assertFalse($redis->get('testDecrease'));
        self::assertSame(-1, $redis->decrease('testDecrease'));
        self::assertSame(-101, $redis->decrease('testDecrease', 100));
        self::assertSame(-201, $redis->decrease('testDecrease', 100));
        $redis->delete('testDecrease');
        self::assertFalse($redis->get('testIncrease'));
    }

    public function testDecreaseWithExpire(): void
    {
        $redis = $this->makeRedis();
        self::assertFalse($redis->get('testDecreaseWithExpire'));
        self::assertSame(-1, $redis->decrease('testDecreaseWithExpire', 1, 1));
        self::assertSame(-1, $redis->get('testDecreaseWithExpire'));

        // 0.7 秒未过期
        usleep(700000);
        self::assertSame(-1, $redis->get('testDecreaseWithExpire'));

        // 1.1 就过期
        usleep(400000);
        self::assertFalse($redis->get('testDecreaseWithExpire'));
    }

    public function testDecreaseReturnFalse(): void
    {
        $redis = $this->makeRedis();
        self::assertFalse($redis->get('testDecreaseReturnFalse'));
        $redis->set('testDecreaseReturnFalse', 'world');
        self::assertFalse($redis->decrease('testDecreaseReturnFalse'));
        $redis->delete('testDecreaseReturnFalse');
        self::assertFalse($redis->get('testDecreaseReturnFalse'));
    }

    public function testHas(): void
    {
        $redis = $this->makeRedis();

        self::assertFalse($redis->has('has'));
        $redis->set('has', 'world');
        self::assertTrue($redis->has('has'));
        $redis->delete('has');
    }

    public function testTtl(): void
    {
        $redis = $this->makeRedis();

        self::assertFalse($redis->has('ttl'));
        self::assertSame(-2, $redis->ttl('ttl'));
        $redis->set('ttl', 'world');
        self::assertSame(-1, $redis->ttl('ttl'));
        $redis->set('ttl', 'world', 1);
        self::assertSame(1, $redis->ttl('ttl'));
        $redis->set('ttl', 'world', 0);
        self::assertSame(-1, $redis->ttl('ttl'));
        $redis->delete('ttl');
    }

    public function testCall(): void
    {
        $redis = $this->makeRedis();

        self::assertSame([], $redis->keys('hello'));
        $redis->set('hello', 'world');
        self::assertSame(['hello'], $redis->keys('hello'));
        $redis->delete('hello');
    }

    public function testSelect(): void
    {
        $redis = $this->makeRedis([
            'select' => 1,
        ]);

        $redis->set('selecttest', 'world');

        self::assertSame('world', $redis->get('selecttest'));

        $redis->delete('selecttest');

        self::assertFalse($redis->get('selecttest'));
    }

    public function testWithPassword(): void
    {
        self::assertTrue(true);
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
