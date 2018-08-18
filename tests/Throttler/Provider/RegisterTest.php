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

namespace Tests\Throttler\Provider;

use Leevel\Cache\Cache;
use Leevel\Cache\File;
use Leevel\Di\Container;
use Leevel\Http\IRequest;
use Leevel\Option\Option;
use Leevel\Throttler\Provider\Register;
use Tests\TestCase;

/**
 * register test.
 *
 * @author Xiangmin Liu <635750556@qq.com>
 *
 * @since 2018.08.15
 *
 * @version 1.0
 */
class RegisterTest extends TestCase
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
        $test = new Register($container = $this->createContainer());

        $test->register();

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

        unlink($path.'/'.$key.'.php');
    }

    protected function createContainer()
    {
        $container = new Container();

        $option = new Option([
            'throttler' => [
                'driver' => 'file',
            ],
        ]);

        $container->singleton('option', $option);

        $container->singleton('caches', new CacheTest());

        $request = $this->createMock(IRequest::class);

        $ip = '127.0.0.1';
        $node = 'foobar';
        $key = sha1($ip.'@'.$node);

        $request->method('getClientIp')->willReturn($ip);
        $this->assertEquals($ip, $request->getClientIp());

        $request->method('getNode')->willReturn($node);
        $this->assertEquals($node, $request->getNode());

        $container->singleton('request', $request);

        return $container;
    }
}

class CacheTest
{
    public function connect($connect): Cache
    {
        return new Cache(new File([
            'path' => __DIR__.'/cache',
        ]));
    }
}
