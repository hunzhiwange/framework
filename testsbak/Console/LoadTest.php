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

namespace Tests\Console;

use Leevel\Console\Load;
use Tests\TestCase;

/**
 * load test.
 *
 * @author Xiangmin Liu <635750556@qq.com>
 *
 * @since 2018.07.23
 *
 * @version 1.0
 */
class LoadTest extends TestCase
{
    public function testBaseUse()
    {
        $load = new Load();

        $load->addNamespace([
            'Tests\Console\Load1' => __DIR__.'/Load1',
            'Tests\Console\Load2' => __DIR__.'/Load2',
        ]);

        $data = $load->loadData();

        $this->assertSame([
            'Tests\Console\Load1\Test1',
            'Tests\Console\Load2\Test1',
            'Tests\Console\Load2\Test2',
        ], $data);

        $data = $load->loadData();

        $this->assertSame([
            'Tests\Console\Load1\Test1',
            'Tests\Console\Load2\Test1',
            'Tests\Console\Load2\Test2',
        ], $data);
    }

    public function testConsoleDirNotFound()
    {
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Console load dir is not exits.');

        $load = new Load();

        $load->addNamespace([
            'Tests\Console\Load1' => __DIR__.'/LoadNotFound',
        ]);

        $load->loadData();
    }
}
