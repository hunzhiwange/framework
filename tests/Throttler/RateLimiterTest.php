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

namespace Tests\Throttler;

use Leevel\Cache\File;
use Leevel\Cache\ICache;
use Leevel\Filesystem\Fso;
use Leevel\Throttler\IRateLimiter;
use Leevel\Throttler\RateLimiter;
use Tests\TestCase;

/**
 * rateLimiter test.
 *
 * @author Xiangmin Liu <635750556@qq.com>
 *
 * @since 2018.08.14
 *
 * @version 1.0
 */
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
            Fso::deleteDirectory($dirPath, true);
        }
    }

    public function testBaseUse()
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
                "X-RateLimit-Time": 60,
                "X-RateLimit-Limit": 60,
                "X-RateLimit-Remaining": 59,
                "X-RateLimit-RetryAfter": 0,
                "X-RateLimit-Reset": {$time}
            }
            eot;

        $header2 = <<<eot
            {
                "X-RateLimit-Time": 60,
                "X-RateLimit-Limit": 60,
                "X-RateLimit-Remaining": 59,
                "X-RateLimit-RetryAfter": 0,
                "X-RateLimit-Reset": {$time2}
            }
            eot;

        $header3 = <<<eot
            {
                "X-RateLimit-Time": 60,
                "X-RateLimit-Limit": 60,
                "X-RateLimit-Remaining": 59,
                "X-RateLimit-RetryAfter": 0,
                "X-RateLimit-Reset": {$time3}
            }
            eot;

        $header0 = <<<eot
            {
                "X-RateLimit-Time": 60,
                "X-RateLimit-Limit": 60,
                "X-RateLimit-Remaining": 59,
                "X-RateLimit-RetryAfter": 0,
                "X-RateLimit-Reset": {$time0}
            }
            eot;

        $this->assertTrue(
            in_array($this->varJson($rateLimiter->header()), [$header, $header2, $header3, $header0], true)
        );

        $this->assertTrue(
            in_array($this->varJson($rateLimiter->toArray()), [$header, $header2, $header3, $header0], true)
        );

        $path = __DIR__.'/cache';

        unlink($path.'/baseuse.php');
    }

    public function testHit()
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
                "X-RateLimit-Time": 60,
                "X-RateLimit-Limit": 60,
                "X-RateLimit-Remaining": 59,
                "X-RateLimit-RetryAfter": 0,
                "X-RateLimit-Reset": {$time}
            }
            eot;

        $header2 = <<<eot
            {
                "X-RateLimit-Time": 60,
                "X-RateLimit-Limit": 60,
                "X-RateLimit-Remaining": 59,
                "X-RateLimit-RetryAfter": 0,
                "X-RateLimit-Reset": {$time2}
            }
            eot;

        $header3 = <<<eot
            {
                "X-RateLimit-Time": 60,
                "X-RateLimit-Limit": 60,
                "X-RateLimit-Remaining": 59,
                "X-RateLimit-RetryAfter": 0,
                "X-RateLimit-Reset": {$time3}
            }
            eot;

        $header0 = <<<eot
            {
                "X-RateLimit-Time": 60,
                "X-RateLimit-Limit": 60,
                "X-RateLimit-Remaining": 59,
                "X-RateLimit-RetryAfter": 0,
                "X-RateLimit-Reset": {$time0}
            }
            eot;

        $this->assertTrue(
            in_array($this->varJson($rateLimiter->header()), [$header, $header2, $header3, $header0], true)
        );

        $cacheData = array_map(function ($v) {
            return (int) ($v);
        }, explode(IRateLimiter::SEPARATE, $cache->get('hit')));

        $this->assertSame($time, $cacheData[0]);
        $this->assertSame(1, $cacheData[1]);

        $rateLimiter->hit();

        $cacheData = array_map(function ($v) {
            return (int) ($v);
        }, explode(IRateLimiter::SEPARATE, $cache->get('hit')));

        $this->assertSame($time, $cacheData[0]);
        $this->assertSame(2, $cacheData[1]);

        $rateLimiter->hit();

        $cacheData = array_map(function ($v) {
            return (int) ($v);
        }, explode(IRateLimiter::SEPARATE, $cache->get('hit')));

        $this->assertSame($time, $cacheData[0]);
        $this->assertSame(3, $cacheData[1]);

        $path = __DIR__.'/cache';

        unlink($path.'/hit.php');
    }

    public function testLimit()
    {
        $rateLimiter = $this->createRateLimiter('limit');

        $this->assertFalse($rateLimiter->attempt());

        $time = time() + 60;
        $time2 = $time + 1;
        $time3 = $time + 2;
        $time0 = $time - 1;

        $header = <<<eot
            {
                "X-RateLimit-Time": 60,
                "X-RateLimit-Limit": 60,
                "X-RateLimit-Remaining": 59,
                "X-RateLimit-RetryAfter": 0,
                "X-RateLimit-Reset": {$time}
            }
            eot;

        $header2 = <<<eot
            {
                "X-RateLimit-Time": 60,
                "X-RateLimit-Limit": 60,
                "X-RateLimit-Remaining": 59,
                "X-RateLimit-RetryAfter": 0,
                "X-RateLimit-Reset": {$time2}
            }
            eot;

        $header3 = <<<eot
            {
                "X-RateLimit-Time": 60,
                "X-RateLimit-Limit": 60,
                "X-RateLimit-Remaining": 59,
                "X-RateLimit-RetryAfter": 0,
                "X-RateLimit-Reset": {$time3}
            }
            eot;

        $header0 = <<<eot
            {
                "X-RateLimit-Time": 60,
                "X-RateLimit-Limit": 60,
                "X-RateLimit-Remaining": 59,
                "X-RateLimit-RetryAfter": 0,
                "X-RateLimit-Reset": {$time0}
            }
            eot;

        $this->assertTimeRange(
            $this->varJson(
                $rateLimiter->header()
            ),
            $header,
            $header2,
            $header3,
            $header0
        );

        $path = __DIR__.'/cache';

        $rateLimiter->limit(80);

        $header = <<<eot
            {
                "X-RateLimit-Time": 60,
                "X-RateLimit-Limit": 80,
                "X-RateLimit-Remaining": 79,
                "X-RateLimit-RetryAfter": 0,
                "X-RateLimit-Reset": {$time}
            }
            eot;

        $header2 = <<<eot
            {
                "X-RateLimit-Time": 60,
                "X-RateLimit-Limit": 80,
                "X-RateLimit-Remaining": 79,
                "X-RateLimit-RetryAfter": 0,
                "X-RateLimit-Reset": {$time2}
            }
            eot;

        $header3 = <<<eot
            {
                "X-RateLimit-Time": 60,
                "X-RateLimit-Limit": 80,
                "X-RateLimit-Remaining": 79,
                "X-RateLimit-RetryAfter": 0,
                "X-RateLimit-Reset": {$time3}
            }
            eot;

        $header0 = <<<eot
            {
                "X-RateLimit-Time": 60,
                "X-RateLimit-Limit": 80,
                "X-RateLimit-Remaining": 79,
                "X-RateLimit-RetryAfter": 0,
                "X-RateLimit-Reset": {$time0}
            }
            eot;

        $this->assertTimeRange(
            $this->varJson(
                $rateLimiter->header()
            ),
            $header,
            $header2,
            $header3,
            $header0
        );

        unlink($path.'/limit.php');

        $this->assertFalse($rateLimiter->attempt());

        $time += 20;
        $time2 = $time + 1;
        $time3 = $time + 2;
        $time0 = $time - 1;

        $header = <<<eot
            {
                "X-RateLimit-Time": 60,
                "X-RateLimit-Limit": 80,
                "X-RateLimit-Remaining": 79,
                "X-RateLimit-RetryAfter": 0,
                "X-RateLimit-Reset": {$time}
            }
            eot;

        $header2 = <<<eot
            {
                "X-RateLimit-Time": 60,
                "X-RateLimit-Limit": 80,
                "X-RateLimit-Remaining": 79,
                "X-RateLimit-RetryAfter": 0,
                "X-RateLimit-Reset": {$time2}
            }
            eot;

        $header3 = <<<eot
            {
                "X-RateLimit-Time": 60,
                "X-RateLimit-Limit": 80,
                "X-RateLimit-Remaining": 79,
                "X-RateLimit-RetryAfter": 0,
                "X-RateLimit-Reset": {$time3}
            }
            eot;

        $header0 = <<<eot
            {
                "X-RateLimit-Time": 60,
                "X-RateLimit-Limit": 80,
                "X-RateLimit-Remaining": 79,
                "X-RateLimit-RetryAfter": 0,
                "X-RateLimit-Reset": {$time0}
            }
            eot;

        $this->assertTimeRange(
            $this->varJson(
                $rateLimiter->header()
            ),
            $header,
            $header2,
            $header3,
            $header0
        );

        unlink($path.'/limit.php');
    }

    public function testTime()
    {
        $rateLimiter = $this->createRateLimiter('time');

        $this->assertFalse($rateLimiter->attempt());

        $time = time() + 60;
        $time2 = $time + 1;
        $time3 = $time + 2;
        $time0 = $time - 1;

        $header = <<<eot
            {
                "X-RateLimit-Time": 60,
                "X-RateLimit-Limit": 60,
                "X-RateLimit-Remaining": 59,
                "X-RateLimit-RetryAfter": 0,
                "X-RateLimit-Reset": {$time}
            }
            eot;

        $header2 = <<<eot
            {
                "X-RateLimit-Time": 60,
                "X-RateLimit-Limit": 60,
                "X-RateLimit-Remaining": 59,
                "X-RateLimit-RetryAfter": 0,
                "X-RateLimit-Reset": {$time2}
            }
            eot;

        $header3 = <<<eot
            {
                "X-RateLimit-Time": 60,
                "X-RateLimit-Limit": 60,
                "X-RateLimit-Remaining": 59,
                "X-RateLimit-RetryAfter": 0,
                "X-RateLimit-Reset": {$time3}
            }
            eot;

        $header0 = <<<eot
            {
                "X-RateLimit-Time": 60,
                "X-RateLimit-Limit": 60,
                "X-RateLimit-Remaining": 59,
                "X-RateLimit-RetryAfter": 0,
                "X-RateLimit-Reset": {$time0}
            }
            eot;

        $this->assertTimeRange(
            $this->varJson(
                $rateLimiter->header()
            ),
            $header,
            $header2,
            $header3,
            $header0
        );

        $path = __DIR__.'/cache';

        $rateLimiter->time(80);

        $header = <<<eot
            {
                "X-RateLimit-Time": 80,
                "X-RateLimit-Limit": 60,
                "X-RateLimit-Remaining": 59,
                "X-RateLimit-RetryAfter": 0,
                "X-RateLimit-Reset": {$time}
            }
            eot;

        $header2 = <<<eot
            {
                "X-RateLimit-Time": 80,
                "X-RateLimit-Limit": 60,
                "X-RateLimit-Remaining": 59,
                "X-RateLimit-RetryAfter": 0,
                "X-RateLimit-Reset": {$time2}
            }
            eot;

        $header3 = <<<eot
            {
                "X-RateLimit-Time": 80,
                "X-RateLimit-Limit": 60,
                "X-RateLimit-Remaining": 59,
                "X-RateLimit-RetryAfter": 0,
                "X-RateLimit-Reset": {$time3}
            }
            eot;

        $header0 = <<<eot
            {
                "X-RateLimit-Time": 80,
                "X-RateLimit-Limit": 60,
                "X-RateLimit-Remaining": 59,
                "X-RateLimit-RetryAfter": 0,
                "X-RateLimit-Reset": {$time0}
            }
            eot;

        $this->assertTimeRange(
            $this->varJson(
                $rateLimiter->header()
            ),
            $header,
            $header2,
            $header3,
            $header0
        );

        unlink($path.'/time.php');

        $this->assertFalse($rateLimiter->attempt());

        $header = <<<eot
            {
                "X-RateLimit-Time": 80,
                "X-RateLimit-Limit": 60,
                "X-RateLimit-Remaining": 59,
                "X-RateLimit-RetryAfter": 0,
                "X-RateLimit-Reset": {$time}
            }
            eot;

        $header2 = <<<eot
            {
                "X-RateLimit-Time": 80,
                "X-RateLimit-Limit": 60,
                "X-RateLimit-Remaining": 59,
                "X-RateLimit-RetryAfter": 0,
                "X-RateLimit-Reset": {$time2}
            }
            eot;

        $header3 = <<<eot
            {
                "X-RateLimit-Time": 80,
                "X-RateLimit-Limit": 60,
                "X-RateLimit-Remaining": 59,
                "X-RateLimit-RetryAfter": 0,
                "X-RateLimit-Reset": {$time3}
            }
            eot;

        $header0 = <<<eot
            {
                "X-RateLimit-Time": 80,
                "X-RateLimit-Limit": 60,
                "X-RateLimit-Remaining": 59,
                "X-RateLimit-RetryAfter": 0,
                "X-RateLimit-Reset": {$time0}
            }
            eot;

        $this->assertTimeRange(
            $this->varJson(
                $rateLimiter->header()
            ),
            $header,
            $header2,
            $header3,
            $header0
        );

        unlink($path.'/time.php');
    }

    public function testKeyIsNotSet()
    {
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage(
            'Key is not set.'
        );

        $rateLimiter = $this->createRateLimiter('');

        $rateLimiter->attempt();
    }

    protected function createRateLimiter(string $key): RateLimiter
    {
        $cache = new File([
            'path' => __DIR__.'/cache',
        ]);

        return new RateLimiter($cache, $key);
    }
}
