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

namespace Tests\Throttler\Provider;

use Leevel\Cache\File;
use Leevel\Cache\ICache;
use Leevel\Di\Container;
use Leevel\Filesystem\Helper;
use Leevel\Http\Request;
use Leevel\Option\Option;
use Leevel\Throttler\Provider\Register;
use Leevel\Throttler\Throttler;
use Tests\TestCase;

class RegisterTest extends TestCase
{
    protected function setUp(): void
    {
        $this->tearDown();
    }

    protected function tearDown(): void
    {
        $dirPath = __DIR__.'/cache';
        if (is_dir($dirPath)) {
            Helper::deleteDirectory($dirPath, true);
        }
    }

    public function testBaseUse(): void
    {
        $test = new Register($container = $this->createContainer());
        $test->register();
        $container->alias($test->providers());

        $throttler = $container->make('throttler');

        $ip = '127.0.0.1';
        $node = 'foobar';
        $key = sha1($ip.'@'.$node);

        $this->assertFalse($throttler->attempt());
        $this->assertFalse($throttler->tooManyAttempt());

        for ($i = 0; $i < 58; $i++) {
            $throttler->hit();
        }

        $this->assertFalse($throttler->attempt());
        $this->assertFalse($throttler->tooManyAttempt());

        $throttler->hit();

        $this->assertTrue($throttler->attempt());
        $this->assertTrue($throttler->tooManyAttempt());

        $path = __DIR__.'/cache';
        $this->assertTrue(is_file($path.'/'.$key.'.php'));

        // alias
        $throttler = $container->make(Throttler::class);
        $this->assertInstanceOf(Throttler::class, $throttler);
    }

    protected function createContainer(): Container
    {
        $container = new Container();

        $option = new Option([
            'throttler' => [
                'driver' => 'file',
            ],
        ]);

        $container->singleton('option', $option);

        $container->singleton('caches', new CacheTest());

        $request = $this->createMock(Request::class);

        $ip = '127.0.0.1';
        $node = 'foobar';

        $request->method('getClientIp')->willReturn($ip);
        $this->assertEquals($ip, $request->getClientIp());

        $request->method('getBaseUrl')->willReturn($node);
        $this->assertEquals($node, $request->getBaseUrl());

        $container->singleton('request', $request);

        return $container;
    }
}

class CacheTest
{
    public function connect($connect): ICache
    {
        return new File([
            'path' => __DIR__.'/cache',
        ]);
    }
}
