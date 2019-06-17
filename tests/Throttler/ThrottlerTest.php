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
use Leevel\Http\IRequest;
use Leevel\Throttler\Throttler;
use Tests\TestCase;

/**
 * throttler test.
 *
 * @author Xiangmin Liu <635750556@qq.com>
 *
 * @since 2018.08.14
 *
 * @version 1.0
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

        $request = $this->createMock(IRequest::class);

        $ip = '127.0.0.1';
        $node = 'foobar';
        $key = sha1($ip.'@'.$node);

        $request->method('getClientIp')->willReturn($ip);
        $this->assertEquals($ip, $request->getClientIp());

        $request->method('getRoot')->willReturn($node);
        $this->assertEquals($node, $request->getRoot());

        $throttler->setRequest($request);

        $this->assertFalse($throttler->attempt());
        $this->assertFalse($throttler->tooManyAttempt());

        $path = __DIR__.'/cache2';

        unlink($path.'/'.$key.'.php');
    }

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
