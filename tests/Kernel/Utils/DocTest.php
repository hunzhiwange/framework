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

namespace Tests\Kernel\Utils;

use Leevel\Filesystem\Helper;
use Leevel\Kernel\Utils\Doc;
use Tests\Kernel\Utils\Assert\Doc\Demo1;
use Tests\Kernel\Utils\Assert\Doc\Demo2;
use Tests\Kernel\Utils\Assert\Doc\Demo3;
use Tests\TestCase;

class DocTest extends TestCase
{
    protected function setUp(): void
    {
        $dirs = [
            __DIR__.'/Assert/Doc/Doc',
        ];
        foreach ($dirs as $dir) {
            if (is_dir($dir)) {
                Helper::deleteDirectory($dir);
            }
        }
    }

    protected function tearDown(): void
    {
        $this->setUp();
    }

    public function testBaseUse(): void
    {
        $doc = new Doc(
            __DIR__.'/Assert/Doc/{i18n}',
            'https://github.com/hunzhiwange/framework/blob/master',
            'zh-CN',
            'zh-CN',
        );
        $result = $doc->handle(Demo1::class);
        $this->assertSame(file_get_contents(__DIR__.'/Assert/Doc/Demo1.md'), $result);
    }

    public function testDoc(): void
    {
        $doc = new Doc(
            __DIR__.'/Assert/Doc/{i18n}',
            'https://github.com/hunzhiwange/framework/blob/master',
            'zh-CN',
            'zh-CN',
        );
        $result = $doc->handle(Demo2::class);
        $this->assertSame(file_get_contents(__DIR__.'/Assert/Doc/Demo2.md'), $result);
    }

    public function testExecutePhpCode(): void
    {
        $doc = new Doc(
            __DIR__.'/Assert/Doc/{i18n}',
            'https://github.com/hunzhiwange/framework/blob/master',
            'zh-CN',
            'zh-CN',
        );
        $result = $doc->handle(Demo3::class);
        $this->assertSame(file_get_contents(__DIR__.'/Assert/Doc/Demo3.md'), $result);
    }

    public function testHandleAndSave(): void
    {
        $doc = new Doc(
            __DIR__.'/Assert/Doc/Doc{i18n}',
            'https://github.com/hunzhiwange/framework/blob/master',
            'zh-CN',
            'zh-CN',
        );
        $result = $doc->handleAndSave(Demo3::class, 'demo3');
        $docFile = __DIR__.'/Assert/Doc/Doc/zh-CN/demo3.md';
        $this->assertSame($result[0], $docFile);
        $this->assertFileExists($docFile);
        $this->assertSame(file_get_contents($docFile), $result[1]);
    }

    public function testHandleAndSaveWithCustomPath(): void
    {
        $doc = new Doc(
            __DIR__.'/Assert/Doc/Doc{i18n}',
            'https://github.com/hunzhiwange/framework/blob/master',
            'zh-CN',
            'zh-CN',
        );
        $result = $doc->handleAndSave(Demo3::class, 'demo3_custom');
        $docFile = __DIR__.'/Assert/Doc/Doc/zh-CN/demo3_custom.md';
        $this->assertSame($result[0], $docFile);
        $this->assertFileExists($docFile);
        $this->assertSame(file_get_contents($docFile), $result[1]);
    }
}
