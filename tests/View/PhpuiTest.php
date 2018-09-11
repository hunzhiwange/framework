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

namespace Tests\View;

use Leevel\View\Phpui;
use Tests\TestCase;

/**
 * phpui test.
 *
 * @author Xiangmin Liu <635750556@qq.com>
 *
 * @since 2018.08.01
 *
 * @version 1.0
 */
class PhpuiTest extends TestCase
{
    public function testBaseUse()
    {
        $phpui = new Phpui([
            'theme_path' => __DIR__.'/assert',
        ]);

        $phpui->setVar('foo', 'bar');

        ob_start();

        $phpui->display('phpui_test');
        $result = ob_get_contents();

        ob_end_clean();

        $this->assertSame('hello phpui,bar.', $result);
    }

    public function testDisplayReturn()
    {
        $phpui = new Phpui([
            'theme_path' => __DIR__.'/assert',
        ]);

        $phpui->setVar('foo', 'bar');

        $result = $phpui->display('phpui_test', [], null, false);

        $this->assertSame('hello phpui,bar.', $result);
    }

    public function testDisplayWithVar()
    {
        $phpui = new Phpui([
            'theme_path' => __DIR__.'/assert',
        ]);

        $result = $phpui->display('phpui_test', ['foo' => 'bar'], null, false);

        $this->assertSame('hello phpui,bar.', $result);
    }

    public function testSetOption()
    {
        $phpui = new Phpui([
            'theme_path' => __DIR__.'/assert',
        ]);

        $phpui->setOption('suffix', '.foo');

        $result = $phpui->display('phpui_test', ['foo' => 'bar'], null, false);

        $this->assertSame('hello phpui for foo suffix,bar.', $result);
    }

    public function testParseFileForFullPath()
    {
        $phpui = new Phpui([
            'theme_path' => __DIR__.'/assert',
        ]);

        $result = $phpui->display(__DIR__.'/assert/phpui_test_pullpath.php', ['foo' => 'bar'], null, false);

        $this->assertSame('hello phpui for fullpath,bar.', $result);
    }

    public function testParseFileThemeNotSet()
    {
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage(
            'Theme path must be set.'
        );

        $phpui = new Phpui();

        $phpui->display('phpui_test_not_found', ['foo' => 'bar'], null, false);
    }

    public function testParseFileFullPathNotFound()
    {
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage(
            'Template file phpui_test_not_found.php does not exist.'
        );

        $phpui = new Phpui([
            'theme_path' => __DIR__.'/assert',
        ]);

        $phpui->display('phpui_test_not_found.php', ['foo' => 'bar'], null, false);
    }

    public function testParseFileForTheme()
    {
        $phpui = new Phpui([
            'theme_path' => __DIR__.'/assert',
        ]);

        $result = $phpui->display('sub/phpui_test_sub', ['foo' => 'bar'], null, false);

        $this->assertSame('hello phpui for sub,bar.', $result);
    }
}
