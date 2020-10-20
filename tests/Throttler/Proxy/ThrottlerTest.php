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

namespace Tests\Throttler\Proxy;

use Leevel\Cache\File;
use Leevel\Cache\ICache;
use Leevel\Di\Container;
use Leevel\Filesystem\Helper;
use Leevel\Throttler\Proxy\Throttler as ProxyThrottler;
use Leevel\Throttler\Throttler;
use Tests\TestCase;

class ThrottlerTest extends TestCase
{
    protected function setUp(): void
    {
        $this->tearDown();
    }

    protected function tearDown(): void
    {
        Container::singletons()->clear();

        $dirPath = __DIR__.'/cache2';
        if (is_dir($dirPath)) {
            Helper::deleteDirectory($dirPath);
        }
    }

    public function testBaseUse(): void
    {
        $throttler = $this->createRateLimiter();
        $container = $this->createContainer();
        $container->singleton('throttler', function () use ($throttler): Throttler {
            return $throttler;
        });

        $rateLimiter = $throttler->create('baseuse');
        $this->assertFalse($rateLimiter->attempt());
        $this->assertFalse($rateLimiter->tooManyAttempt());
        $this->assertInstanceof(ICache::class, $rateLimiter->getCache());

        // with_cache
        $this->assertCount(1, $this->getTestProperty($throttler, 'rateLimiter'));
        $rateLimiter2 = $throttler->create('baseuse');
        $this->assertFalse($rateLimiter2->attempt());
        $this->assertFalse($rateLimiter2->tooManyAttempt());
        $this->assertCount(1, $this->getTestProperty($throttler, 'rateLimiter'));
    }

    public function testProxy(): void
    {
        $throttler = $this->createRateLimiter();
        $container = $this->createContainer();
        $container->singleton('throttler', function () use ($throttler): Throttler {
            return $throttler;
        });

        $rateLimiter = ProxyThrottler::create('baseuse');
        $this->assertFalse($rateLimiter->attempt());
        $this->assertFalse($rateLimiter->tooManyAttempt());
        $this->assertInstanceof(ICache::class, $rateLimiter->getCache());

        // with_cache
        $this->assertCount(1, $this->getTestProperty($throttler, 'rateLimiter'));
        $rateLimiter2 = ProxyThrottler::create('baseuse');
        $this->assertFalse($rateLimiter2->attempt());
        $this->assertFalse($rateLimiter2->tooManyAttempt());
        $this->assertCount(1, $this->getTestProperty($throttler, 'rateLimiter'));
    }

    protected function createRateLimiter(): Throttler
    {
        $cache = new File([
            'path' => __DIR__.'/cache2',
        ]);

        return new Throttler($cache);
    }

    protected function createContainer(): Container
    {
        $container = Container::singletons();
        $container->clear();

        return $container;
    }
}