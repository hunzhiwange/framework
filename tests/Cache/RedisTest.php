<?php

declare(strict_types=1);

namespace Tests\Cache;

use Leevel\Cache\Redis;
use Leevel\Cache\Redis\IRedis;
use Tests\TestCase;

/**
 * @internal
 *
 * @coversNothing
 */
final class RedisTest extends TestCase
{
    public function testBaseUse(): void
    {
        $phpRedis = $this->createMock(IRedis::class);
        $this->assertInstanceof(IRedis::class, $phpRedis);

        $phpRedis->method('get')->willReturn(false);
        static::assertFalse($phpRedis->get('foo'));

        $redis = $this->makeRedis($phpRedis);
        static::assertFalse($redis->get('foo'));
    }

    public function testGet(): void
    {
        $phpRedis = $this->createMock(IRedis::class);
        $this->assertInstanceof(IRedis::class, $phpRedis);

        $phpRedis->method('get')->willReturn('"bar"');
        static::assertSame('"bar"', $phpRedis->get('foo'));

        $redis = $this->makeRedis($phpRedis);
        static::assertSame('bar', $redis->get('foo'));
    }

    public function testGetInt(): void
    {
        $phpRedis = $this->createMock(IRedis::class);
        $this->assertInstanceof(IRedis::class, $phpRedis);

        $phpRedis->method('get')->willReturn('1');
        static::assertSame('1', $phpRedis->get('num'));

        $redis = $this->makeRedis($phpRedis);
        static::assertSame(1, $redis->get('num'));
    }

    public function testGetWithException(): void
    {
        $this->expectException(\TypeError::class);
        $this->expectExceptionMessage(
            'json_decode(): Argument #1 ($json) must be of type string, int given'
        );

        $phpRedis = $this->createMock(IRedis::class);
        $this->assertInstanceof(IRedis::class, $phpRedis);

        $phpRedis->method('get')->willReturn(5);
        static::assertSame(5, $phpRedis->get('foo'));

        $redis = $this->makeRedis($phpRedis);
        $redis->get('foo');
    }

    public function testSet(): void
    {
        $phpRedis = $this->createMock(IRedis::class);
        $this->assertInstanceof(IRedis::class, $phpRedis);
        static::assertNull($phpRedis->set('foo', 'bar', 60));

        $redis = $this->makeRedis($phpRedis);
        static::assertNull($redis->set('foo', 'bar', 60));
    }

    public function testDelete(): void
    {
        $phpRedis = $this->createMock(IRedis::class);
        $this->assertInstanceof(IRedis::class, $phpRedis);
        static::assertNull($phpRedis->delete('foo'));

        $redis = $this->makeRedis($phpRedis);
        static::assertNull($redis->delete('foo'));
    }

    public function testHas(): void
    {
        $phpRedis = $this->createMock(IRedis::class);
        $this->assertInstanceof(IRedis::class, $phpRedis);
        $phpRedis->method('has')->willReturn(true);
        static::assertTrue($phpRedis->has('foo'));

        $redis = $this->makeRedis($phpRedis);
        static::assertTrue($redis->has('foo'));
    }

    public function testTtl(): void
    {
        $phpRedis = $this->createMock(IRedis::class);
        $this->assertInstanceof(IRedis::class, $phpRedis);
        $phpRedis->method('ttl')->willReturn(80);
        static::assertSame(80, $phpRedis->ttl('foo'));

        $redis = $this->makeRedis($phpRedis);
        static::assertSame(80, $redis->ttl('foo'));
    }

    public function testIncrease(): void
    {
        $phpRedis = $this->createMock(IRedis::class);
        $this->assertInstanceof(IRedis::class, $phpRedis);

        $phpRedis->method('increase')->willReturn(100);
        static::assertSame(100, $phpRedis->increase('foo', 100, 5));

        $redis = $this->makeRedis($phpRedis);
        static::assertSame(100, $redis->increase('foo'));
    }

    public function testDecrease(): void
    {
        $phpRedis = $this->createMock(IRedis::class);
        $this->assertInstanceof(IRedis::class, $phpRedis);

        $phpRedis->method('decrease')->willReturn(-100);
        static::assertSame(-100, $phpRedis->decrease('foo', 100, 5));

        $redis = $this->makeRedis($phpRedis);
        static::assertSame(-100, $redis->decrease('foo'));
    }

    public function testClose(): void
    {
        $phpRedis = $this->createMock(IRedis::class);
        $this->assertInstanceof(IRedis::class, $phpRedis);
        static::assertNull($phpRedis->close());

        $redis = $this->makeRedis($phpRedis);
        static::assertNull($redis->close());
        static::assertNull($redis->close()); // 关闭多次不做任何事
    }

    protected function makeRedis(IRedis $phpRedis, array $option = []): Redis
    {
        $default = [
            'expire' => 86400,
        ];
        $option = array_merge($default, $option);

        return new Redis($phpRedis, $option);
    }
}
