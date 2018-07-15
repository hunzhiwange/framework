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

namespace Tests\Support\Debug;

use Leevel\Support\Debug\Console;
use Tests\TestCase;

/**
 * console test.
 *
 * @author Xiangmin Liu <635750556@qq.com>
 *
 * @since 2018.07.15
 *
 * @version 1.0
 */
class ConsoleTest extends TestCase
{
    public function testBaseUse()
    {
        $this->assertNull(Console::trace([]));

        $_SERVER['SERVER_SOFTWARE'] = 'swoole-http-server';
        $_SERVER['HTTP_X_REQUESTED_WITH'] = 'xmlhttprequest';

        $this->assertNull(Console::trace([]));

        unset($_SERVER['HTTP_X_REQUESTED_WITH']);

        $content = Console::trace([
            'sql' => ['hello', 'world'],
        ]);

        $this->assertContains('<script type="text/javascript">', $content);

        $this->assertContains('LOADED.FILE', $content);

        $this->assertContains('SQL.LOG (2)', $content);

        unset($_SERVER['SERVER_SOFTWARE']);
    }

    public function testJsonTrace()
    {
        $content = Console::jsonTrace([
            'sql' => ['hello', 'world'],
        ]);

        $this->assertInternalType('array', $content);

        $this->assertArrayHasKey('SQL.LOG (2)', $content);
    }
}
