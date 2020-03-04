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

namespace Tests\Throttler;

use Leevel\Cache\File;
use Leevel\Cache\ICache;
use Leevel\Http\Request;
use Leevel\Throttler\Throttler;
use Tests\TestCase;

/**
 * @api(
 *     title="节流器",
 *     path="component/throttler",
 *     description="
 * 节流器主要通过路由服务提供者来调用节流器中间件 `throttler:60,1` 实现限速。
 *
 * 路由服务提供者 **Common\Infra\Provider\Router**
 *
 * ``` php
 * {[file_get_contents('common/Infra/Provider/Router.php')]}
 * ```
 * ",
 * )
 */
class ThrottlerTest extends TestCase
{
    protected function tearDown(): void
    {
        $dirPath = __DIR__.'/cache2';
        if (is_dir($dirPath)) {
            rmdir($dirPath);
        }
    }

    /**
     * @api(
     *     title="基本使用",
     *     description="
     * 节流器主要通过 `attempt` 和 `tooManyAttempt` 来执行限制请求。
     *
     * **attempt 原型**
     *
     * ``` php
     * {[\Leevel\Kernel\Utils\Doc::getMethodBody(\Leevel\Throttler\RateLimiter::class, 'attempt', 'define')]}
     * ```
     *
     * **tooManyAttempt 原型**
     *
     * ``` php
     * {[\Leevel\Kernel\Utils\Doc::getMethodBody(\Leevel\Throttler\RateLimiter::class, 'tooManyAttempt', 'define')]}
     * ```
     * ",
     *     note="",
     * )
     */
    public function testBaseUse(): void
    {
        $throttler = $this->createRateLimiter();

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

        $path = __DIR__.'/cache2';

        unlink($path.'/baseuse.php');
    }

    public function testUseCall(): void
    {
        $throttler = $this->createRateLimiter();

        $request = $this->createMock(Request::class);

        $ip = '127.0.0.1';
        $node = 'foobar';
        $key = sha1($ip.'@'.$node);

        $request->method('getClientIp')->willReturn($ip);
        $this->assertEquals($ip, $request->getClientIp());

        $request->method('getBaseUrl')->willReturn($node);
        $this->assertEquals($node, $request->getBaseUrl());

        $throttler->setRequest($request);

        $this->assertFalse($throttler->attempt());
        $this->assertFalse($throttler->tooManyAttempt());

        $path = __DIR__.'/cache2';

        unlink($path.'/'.$key.'.php');
    }

    /**
     * @api(
     *     title="限流例子",
     *     description="",
     *     note="",
     * )
     */
    public function testAttempt(): void
    {
        $throttler = $this->createRateLimiter();

        $rateLimiter = $throttler->create('attempt', 2, 1);

        for ($i = 0; $i < 10; $i++) {
            $rateLimiter->hit();
        }

        $this->assertTrue($rateLimiter->attempt());
        $this->assertTrue($rateLimiter->tooManyAttempt());

        $path = __DIR__.'/cache2';

        unlink($path.'/attempt.php');
    }

    public function testRequestIsNotSet(): void
    {
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage(
            'Request is not set.'
        );

        $throttler = $this->createRateLimiter();

        $throttler->attempt();
    }

    protected function createRateLimiter(): Throttler
    {
        $cache = new File([
            'path' => __DIR__.'/cache2',
        ]);

        return new Throttler($cache);
    }
}
