<?php

declare(strict_types=1);

namespace Tests\Throttler;

use Leevel\Cache\File;
use Leevel\Cache\ICache;
use Leevel\Filesystem\Helper;
use Leevel\Throttler\RateLimiter;
use Tests\TestCase;

class RateLimiterTest extends TestCase
{
    protected function setUp(): void
    {
        $this->tearDown();
    }

    protected function tearDown(): void
    {
        $dirPath = __DIR__.'/cache';
        if (is_dir($dirPath)) {
            Helper::deleteDirectory($dirPath);
        }
    }

    public function testBaseUse(): void
    {
        $rateLimiter = $this->createRateLimiter('baseuse');

        $this->assertFalse($rateLimiter->attempt());
        $this->assertFalse($rateLimiter->tooManyAttempt());
        $this->assertInstanceof(ICache::class, $rateLimiter->getCache());

        $time = time() + 60;
        $time2 = $time + 1;
        $time3 = $time + 2;
        $time0 = $time - 1;

        $header = <<<eot
            {
                "X-RateLimit-Limit": 60,
                "X-RateLimit-Remaining": 59,
                "X-RateLimit-Reset": {$time}
            }
            eot;

        $header2 = <<<eot
            {
                "X-RateLimit-Limit": 60,
                "X-RateLimit-Remaining": 59,
                "X-RateLimit-Reset": {$time2}
            }
            eot;

        $header3 = <<<eot
            {
                "X-RateLimit-Limit": 60,
                "X-RateLimit-Remaining": 59,
                "X-RateLimit-Reset": {$time3}
            }
            eot;

        $header0 = <<<eot
            {
                "X-RateLimit-Limit": 60,
                "X-RateLimit-Remaining": 59,
                "X-RateLimit-Reset": {$time0}
            }
            eot;

        $this->assertTrue(
            in_array($this->varJson($rateLimiter->getHeaders()), [$header, $header2, $header3, $header0], true)
        );
    }

    public function testHit(): void
    {
        $rateLimiter = $this->createRateLimiter('hit');
        $cache = $rateLimiter->getCache();

        $this->assertFalse($rateLimiter->attempt());

        $time = time() + 60;
        $time2 = $time + 1;
        $time3 = $time + 2;
        $time0 = $time - 1;

        $header = <<<eot
            {
                "X-RateLimit-Limit": 60,
                "X-RateLimit-Remaining": 59,
                "X-RateLimit-Reset": {$time}
            }
            eot;

        $header2 = <<<eot
            {
                "X-RateLimit-Limit": 60,
                "X-RateLimit-Remaining": 59,
                "X-RateLimit-Reset": {$time2}
            }
            eot;

        $header3 = <<<eot
            {
                "X-RateLimit-Limit": 60,
                "X-RateLimit-Remaining": 59,
                "X-RateLimit-Reset": {$time3}
            }
            eot;

        $header0 = <<<eot
            {
                "X-RateLimit-Limit": 60,
                "X-RateLimit-Remaining": 59,
                "X-RateLimit-Reset": {$time0}
            }
            eot;

        $this->assertTrue(
            in_array($this->varJson($rateLimiter->getHeaders()), [$header, $header2, $header3, $header0], true)
        );

        $cacheData = array_map(function ($v) {
            return (int) ($v);
        }, json_decode($cache->get('hit'), true));

        $this->assertSame($time, $cacheData[0]);
        $this->assertSame(1, $cacheData[1]);

        $rateLimiter->hit();

        $cacheData = array_map(function ($v) {
            return (int) ($v);
        }, json_decode($cache->get('hit'), true));

        $this->assertSame($time, $cacheData[0]);
        $this->assertSame(2, $cacheData[1]);

        $rateLimiter->hit();

        $cacheData = array_map(function ($v) {
            return (int) ($v);
        }, json_decode($cache->get('hit'), true));

        $this->assertSame($time, $cacheData[0]);
        $this->assertSame(3, $cacheData[1]);
    }

    public function testSetLimit(): void
    {
        $rateLimiter = $this->createRateLimiter('limit');
        $this->assertFalse($rateLimiter->attempt());

        $time = time() + 60;
        $time2 = $time + 1;
        $time3 = $time + 2;
        $time0 = $time - 1;

        $header = <<<eot
            {
                "X-RateLimit-Limit": 60,
                "X-RateLimit-Remaining": 59,
                "X-RateLimit-Reset": {$time}
            }
            eot;

        $header2 = <<<eot
            {
                "X-RateLimit-Limit": 60,
                "X-RateLimit-Remaining": 59,
                "X-RateLimit-Reset": {$time2}
            }
            eot;

        $header3 = <<<eot
            {
                "X-RateLimit-Limit": 60,
                "X-RateLimit-Remaining": 59,
                "X-RateLimit-Reset": {$time3}
            }
            eot;

        $header0 = <<<eot
            {
                "X-RateLimit-Limit": 60,
                "X-RateLimit-Remaining": 59,
                "X-RateLimit-Reset": {$time0}
            }
            eot;

        $this->assertTimeRange(
            $this->varJson(
                $rateLimiter->getHeaders()
            ),
            $header,
            $header2,
            $header3,
            $header0
        );

        $rateLimiter->setLimit(80);

        $header = <<<eot
            {
                "X-RateLimit-Limit": 80,
                "X-RateLimit-Remaining": 79,
                "X-RateLimit-Reset": {$time}
            }
            eot;

        $header2 = <<<eot
            {
                "X-RateLimit-Limit": 80,
                "X-RateLimit-Remaining": 79,
                "X-RateLimit-Reset": {$time2}
            }
            eot;

        $header3 = <<<eot
            {
                "X-RateLimit-Limit": 80,
                "X-RateLimit-Remaining": 79,
                "X-RateLimit-Reset": {$time3}
            }
            eot;

        $header0 = <<<eot
            {
                "X-RateLimit-Limit": 80,
                "X-RateLimit-Remaining": 79,
                "X-RateLimit-Reset": {$time0}
            }
            eot;

        $this->assertTimeRange(
            $this->varJson(
                $rateLimiter->getHeaders()
            ),
            $header,
            $header2,
            $header3,
            $header0
        );
    }

    public function testSetTime(): void
    {
        $rateLimiter = $this->createRateLimiter('time');
        $this->assertFalse($rateLimiter->attempt());

        $time = time() + 60;
        $time2 = $time + 1;
        $time3 = $time + 2;
        $time0 = $time - 1;

        $header = <<<eot
            {
                "X-RateLimit-Limit": 60,
                "X-RateLimit-Remaining": 59,
                "X-RateLimit-Reset": {$time}
            }
            eot;

        $header2 = <<<eot
            {
                "X-RateLimit-Limit": 60,
                "X-RateLimit-Remaining": 59,
                "X-RateLimit-Reset": {$time2}
            }
            eot;

        $header3 = <<<eot
            {
                "X-RateLimit-Limit": 60,
                "X-RateLimit-Remaining": 59,
                "X-RateLimit-Reset": {$time3}
            }
            eot;

        $header0 = <<<eot
            {
                "X-RateLimit-Limit": 60,
                "X-RateLimit-Remaining": 59,
                "X-RateLimit-Reset": {$time0}
            }
            eot;

        $this->assertTimeRange(
            $this->varJson(
                $rateLimiter->getHeaders()
            ),
            $header,
            $header2,
            $header3,
            $header0
        );

        $rateLimiter->setTime(80);

        $header = <<<eot
            {
                "X-RateLimit-Limit": 60,
                "X-RateLimit-Remaining": 59,
                "X-RateLimit-Reset": {$time}
            }
            eot;

        $header2 = <<<eot
            {
                "X-RateLimit-Limit": 60,
                "X-RateLimit-Remaining": 59,
                "X-RateLimit-Reset": {$time2}
            }
            eot;

        $header3 = <<<eot
            {
                "X-RateLimit-Limit": 60,
                "X-RateLimit-Remaining": 59,
                "X-RateLimit-Reset": {$time3}
            }
            eot;

        $header0 = <<<eot
            {
                "X-RateLimit-Limit": 60,
                "X-RateLimit-Remaining": 59,
                "X-RateLimit-Reset": {$time0}
            }
            eot;

        $this->assertTimeRange(
            $this->varJson(
                $rateLimiter->getHeaders()
            ),
            $header,
            $header2,
            $header3,
            $header0
        );
    }

    public function testSetLimitWithException(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage(
            'Param `$limit` must be greater than 0.'
        );

        $rateLimiter = $this->createRateLimiter('time');
        $rateLimiter->setLimit(0);
    }

    public function testSetTimeWithException(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage(
            'Param `$time` must be greater than 0.'
        );

        $rateLimiter = $this->createRateLimiter('time');
        $rateLimiter->setTime(0);
    }

    public function testKeyIsNotSet(): void
    {
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage(
            'Rate limiter key must be not empty.'
        );

        $rateLimiter = $this->createRateLimiter('');
        $rateLimiter->attempt();
    }

    public function testGetCount(): void
    {
        $rateLimiter = $this->createRateLimiter('time');
        $this->assertSame(0, $rateLimiter->getCount());
        $this->assertFalse($rateLimiter->attempt());
        $this->assertSame(1, $rateLimiter->getCount());
    }

    protected function createRateLimiter(string $key): RateLimiter
    {
        $cache = new File([
            'path' => __DIR__.'/cache',
        ]);

        return new RateLimiter($cache, $key, 60, 60);
    }
}
