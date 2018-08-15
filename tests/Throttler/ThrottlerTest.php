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
    protected function tearDown()
    {
        $dirPath = __DIR__.'/cache2';

        if (is_dir($dirPath)) {
            rmdir($dirPath);
        }
    }

    public function testBaseUse()
    {
        $throttler = $this->createRateLimiter();

        $rateLimiter = $throttler->create('baseuse');

        $this->assertFalse($rateLimiter->attempt());
        $this->assertFalse($rateLimiter->tooManyAttempt());
        $this->assertInstanceof(Cache::class, $rateLimiter->getCache());

        // with_cache
        $this->assertSame(1, count($this->getTestProperty($throttler, 'rateLimiter')));
        $rateLimiter2 = $throttler->create('baseuse');
        $this->assertFalse($rateLimiter2->attempt());
        $this->assertFalse($rateLimiter2->tooManyAttempt());
        $this->assertSame(1, count($this->getTestProperty($throttler, 'rateLimiter')));

        $path = __DIR__.'/cache2';

        unlink($path.'/_baseuse.php');
    }

    public function testUseCall()
    {
        $throttler = $this->createRateLimiter();

        $request = $this->createMock(IRequest::class);

        $ip = '127.0.0.1';
        $node = 'foobar';
        $key = sha1($ip.'@'.$node);

        $request->method('getClientIp')->willReturn($ip);
        $this->assertEquals($ip, $request->getClientIp());

        $request->method('getNode')->willReturn($node);
        $this->assertEquals($node, $request->getNode());

        $throttler->setRequest($request);

        $this->assertFalse($throttler->attempt());
        $this->assertFalse($throttler->tooManyAttempt());

        $path = __DIR__.'/cache2';

        unlink($path.'/_'.$key.'.php');
    }

    public function testAttempt()
    {
        $throttler = $this->createRateLimiter();

        $rateLimiter = $throttler->create('attempt', 2, 1);

        for ($i = 0; $i < 10; $i++) {
            $rateLimiter->hit();
        }

        $this->assertTrue($rateLimiter->attempt());
        $this->assertTrue($rateLimiter->tooManyAttempt());

        $path = __DIR__.'/cache2';

        unlink($path.'/_attempt.php');
    }

    public function testRequestIsNotSet()
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
        $cache = new Cache(new File([
            'path' => __DIR__.'/cache2',
        ]));

        return new Throttler($cache);
    }
}
