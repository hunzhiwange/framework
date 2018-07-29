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

namespace Tests\Cache;

use Leevel\Cache\Cache;
use Leevel\Cache\File;
use Tests\TestCase;

/**
 * cache test.
 *
 * @author Xiangmin Liu <635750556@qq.com>
 *
 * @since 2018.07.29
 *
 * @version 1.0
 */
class CacheTest extends TestCase
{
    public function testBaseUse()
    {
        $filePath = __DIR__.'/_hello.php';

        $cache = new Cache(new File([
            'path' => __DIR__,
        ]));

        $cache->set('hello', 'world');

        $this->assertTrue(is_file($filePath));

        $this->assertSame('world', $cache->get('hello'));

        $cache->delete('hello');

        $this->assertFalse(is_file($filePath));

        $this->assertFalse($cache->get('hello'));
    }

    public function testPut()
    {
        $cache = new Cache(new File([
            'path' => __DIR__,
        ]));

        $cache->put('hello', 'world');

        $cache->put(['hello2' => 'world', 'foo' => 'bar']);

        $this->assertSame('world', $cache->get('hello'));
        $this->assertSame('world', $cache->get('hello2'));
        $this->assertSame('bar', $cache->get('foo'));

        $cache->delete('hello');
        $cache->delete('hello2');
        $cache->delete('foo');

        $this->assertFalse($cache->get('hello'));
        $this->assertFalse($cache->get('hello2'));
        $this->assertFalse($cache->get('foo'));
    }

    public function testMacro()
    {
        $cache = new Cache(new File([
            'path' => __DIR__,
        ]));

        $cache->macro('hello', function ($item) {
            return $item;
        });

        $this->assertSame('world', $cache->hello('world'));
    }
}
