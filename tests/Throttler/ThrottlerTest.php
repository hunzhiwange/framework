<?php

declare(strict_types=1);

namespace Tests\Throttler;

use Leevel\Cache\File;
use Leevel\Cache\ICache;
use Leevel\Filesystem\Helper;
use Leevel\Http\Request;
use Leevel\Throttler\Throttler;
use Tests\TestCase;

/**
 * @api(
 *     zh-CN:title="节流器",
 *     path="component/throttler",
 *     zh-CN:description="
 * 节流器主要通过路由服务提供者来调用节流器中间件 `throttler:60,1` 实现限速。
 *
 * 路由服务提供者 **App\Infra\Provider\Router**
 *
 * ``` php
 * {[file_get_contents('apps/app/Infra/Provider/Router.php')]}
 * ```
 * ",
 * )
 */
class ThrottlerTest extends TestCase
{
    protected function setUp(): void
    {
        $this->tearDown();
    }

    protected function tearDown(): void
    {
        $dirPath = __DIR__.'/cache2';
        if (is_dir($dirPath)) {
            Helper::deleteDirectory($dirPath);
        }
    }

    /**
     * @api(
     *     zh-CN:title="基本使用",
     *     zh-CN:description="
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
     *     zh-CN:note="",
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
    }

    /**
     * @api(
     *     zh-CN:title="限流例子",
     *     zh-CN:description="",
     *     zh-CN:note="",
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
