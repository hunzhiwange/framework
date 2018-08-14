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

namespace Tests\Throttler;

use Leevel\Cache\Cache;
use Leevel\Cache\File;
use Leevel\Throttler\IRateLimiter;
use Leevel\Throttler\Throttler;
use Tests\TestCase;

/**
 * throttler test.
 *
 * @author Xiangmin Liu <635750556@qq.com>
 *
 * @since 6018.08.14
 *
 * @version 1.0
 */
class ThrottlerTest extends TestCase
{
    protected function tearDown()
    {
        $dirPath = __DIR__.'/cache';

        if (is_dir($dirPath)) {
            rmdir($dirPath);
        }
    }

    public function testBaseUse()
    {
        list($cache, $throttler) = $this->createRateLimiter();

        $rateLimiter = $throttler->create('baseuse');

        $this->assertFalse($rateLimiter->attempt());
        $this->assertFalse($rateLimiter->tooManyAttempt());

        $time = time() + 60;

        $header = <<<eot
array (
  'X-RateLimit-Time' => 60,
  'X-RateLimit-Limit' => 60,
  'X-RateLimit-Remaining' => 59,
  'X-RateLimit-RetryAfter' => 0,
  'X-RateLimit-Reset' => {$time},
)
eot;

        $this->assertSame(
            $header,
            $this->varExport(
                $rateLimiter->header()
            )
        );

        $path = __DIR__.'/cache';

        unlink($path.'/_baseuse.php');
    }

    public function testHit()
    {
        list($cache, $throttler) = $this->createRateLimiter();

        $rateLimiter = $throttler->create('hit');

        $this->assertFalse($rateLimiter->attempt());

        $time = time() + 60;

        $header = <<<eot
array (
  'X-RateLimit-Time' => 60,
  'X-RateLimit-Limit' => 60,
  'X-RateLimit-Remaining' => 59,
  'X-RateLimit-RetryAfter' => 0,
  'X-RateLimit-Reset' => {$time},
)
eot;
        $this->assertSame(
            $header,
            $this->varExport(
                $rateLimiter->header()
            )
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

        unlink($path.'/_hit.php');
    }

    public function testLimit()
    {
        list($cache, $throttler) = $this->createRateLimiter();

        $rateLimiter = $throttler->create('limit');

        $this->assertFalse($rateLimiter->attempt());

        $time = time() + 60;

        $header = <<<eot
array (
  'X-RateLimit-Time' => 60,
  'X-RateLimit-Limit' => 60,
  'X-RateLimit-Remaining' => 59,
  'X-RateLimit-RetryAfter' => 0,
  'X-RateLimit-Reset' => {$time},
)
eot;

        $this->assertSame(
            $header,
            $this->varExport(
                $rateLimiter->header()
            )
        );

        $path = __DIR__.'/cache';

        $rateLimiter->limit(80);

        $header = <<<eot
array (
  'X-RateLimit-Time' => 60,
  'X-RateLimit-Limit' => 80,
  'X-RateLimit-Remaining' => 79,
  'X-RateLimit-RetryAfter' => 0,
  'X-RateLimit-Reset' => {$time},
)
eot;

        $this->assertSame(
            $header,
            $this->varExport(
                $rateLimiter->header()
            )
        );

        unlink($path.'/_limit.php');

        $this->assertFalse($rateLimiter->attempt());

        $time += 20;

        $header = <<<eot
array (
  'X-RateLimit-Time' => 60,
  'X-RateLimit-Limit' => 80,
  'X-RateLimit-Remaining' => 79,
  'X-RateLimit-RetryAfter' => 0,
  'X-RateLimit-Reset' => {$time},
)
eot;

        $this->assertSame(
            $header,
            $this->varExport(
                $rateLimiter->header()
            )
        );

        unlink($path.'/_limit.php');
    }

    protected function createRateLimiter(): array
    {
        $cache = new Cache(new File([
            'path' => __DIR__.'/cache',
        ]));

        return [$cache, new Throttler($cache)];
    }
}
