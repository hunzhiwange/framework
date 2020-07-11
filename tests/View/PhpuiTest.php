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

namespace Tests\View;

use Leevel\View\Phpui;
use Tests\TestCase;

class PhpuiTest extends TestCase
{
    public function testBaseUse(): void
    {
        $phpui = new Phpui([
            'theme_path' => __DIR__.'/assert',
        ]);

        $phpui->setVar('foo', 'bar');
        $result = $phpui->display('phpui_test');
        $this->assertSame('hello phpui,bar.', $result);
    }

    public function testDisplayReturn(): void
    {
        $phpui = new Phpui([
            'theme_path' => __DIR__.'/assert',
        ]);

        $phpui->setVar('foo', 'bar');

        $result = $phpui->display('phpui_test');

        $this->assertSame('hello phpui,bar.', $result);
    }

    public function testDisplayWithVar(): void
    {
        $phpui = new Phpui([
            'theme_path' => __DIR__.'/assert',
        ]);

        $result = $phpui->display('phpui_test', ['foo' => 'bar']);

        $this->assertSame('hello phpui,bar.', $result);
    }

    public function testParseFileForFullPath(): void
    {
        $phpui = new Phpui([
            'theme_path' => __DIR__.'/assert',
        ]);

        $result = $phpui->display(__DIR__.'/assert/phpui_test_pullpath.php', ['foo' => 'bar']);

        $this->assertSame('hello phpui for fullpath,bar.', $result);
    }

    public function testParseFileThemeNotSet(): void
    {
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage(
            'Theme path must be set.'
        );

        $phpui = new Phpui();

        $phpui->display('phpui_test_not_found', ['foo' => 'bar']);
    }

    public function testParseFileFullPathNotFound(): void
    {
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage(
            'Template file phpui_test_not_found.php does not exist.'
        );

        $phpui = new Phpui([
            'theme_path' => __DIR__.'/assert',
        ]);

        $phpui->display('phpui_test_not_found.php', ['foo' => 'bar']);
    }

    public function testParseFileForTheme(): void
    {
        $phpui = new Phpui([
            'theme_path' => __DIR__.'/assert',
        ]);

        $result = $phpui->display('sub/phpui_test_sub', ['foo' => 'bar']);

        $this->assertSame('hello phpui for sub,bar.', $result);
    }
}
