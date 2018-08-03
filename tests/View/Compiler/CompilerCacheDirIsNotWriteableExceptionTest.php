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

namespace Tests\View\Compiler;

use Leevel\Filesystem\Fso;
use Tests\TestCase;

/**
 * CompilerCacheDirIsNotWriteableExceptionTest test.
 *
 * @author Xiangmin Liu <635750556@qq.com>
 *
 * @since 2018.08.03
 *
 * @version 1.0
 */
class CompilerCacheDirIsNotWriteableExceptionTest extends TestCase
{
    use Compiler;

    protected function tearDown()
    {
        Fso::deleteDirectory(__DIR__.'/cacheWriteable', true);
    }

    public function testBaseUse()
    {
        $dirname = __DIR__.'/cacheWriteable';

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage(
            sprintf('Dir %s is not writeable.', $dirname)
        );

        $parser = $this->createParser();

        // 设置目录只读
        // 7 = 4+2+1 分别代表可读可写可执行
        mkdir($dirname, 0444);

        $parser->doCompile('hello world', $dirname.'/test.php', true);
    }
}
