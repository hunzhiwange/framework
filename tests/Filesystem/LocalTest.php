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

namespace Tests\Filesystem;

use Leevel\Filesystem\Local;
use Tests\TestCase;

/**
 * local test.
 *
 * @author Xiangmin Liu <635750556@qq.com>
 *
 * @since 2018.07.27
 *
 * @version 1.0
 */
class LocalTest extends TestCase
{
    public function testBaseUse()
    {
        $local = new Local([
            'path' => $path = __DIR__,
        ]);

        $local->put('hello.txt', 'foo');

        $file = $path.'/hello.txt';

        $this->assertTrue(is_file($file));
        $this->assertSame('foo', file_get_contents($file));

        unlink($file);
    }

    public function testPathNotFound()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('The local requires path option.');
        
        $local = new Local([
            'path' => '',
        ]);
    }
}
