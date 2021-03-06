<?php

declare(strict_types=1);

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
            'zh-CN',
            'zh-CN',
            'https://github.com/hunzhiwange/framework/blob/master',
        );
        $result = $doc->handle(Demo1::class);
        $this->assertSame(file_get_contents(__DIR__.'/Assert/Doc/Demo1.md'), $result);
    }

    public function testDoc(): void
    {
        $doc = new Doc(
            __DIR__.'/Assert/Doc/{i18n}',
            'zh-CN',
            'zh-CN',
            'https://github.com/hunzhiwange/framework/blob/master',
        );
        $result = $doc->handle(Demo2::class);
        $this->assertSame(file_get_contents(__DIR__.'/Assert/Doc/Demo2.md'), $result);
    }

    public function testExecutePhpCode(): void
    {
        $doc = new Doc(
            __DIR__.'/Assert/Doc/{i18n}',
            'zh-CN',
            'zh-CN',
            'https://github.com/hunzhiwange/framework/blob/master',
        );
        $result = $doc->handle(Demo3::class);
        $this->assertSame(file_get_contents(__DIR__.'/Assert/Doc/Demo3.md'), $result);
    }

    public function testHandleAndSave(): void
    {
        $doc = new Doc(
            __DIR__.'/Assert/Doc/Doc{i18n}',
            'zh-CN',
            'zh-CN',
            'https://github.com/hunzhiwange/framework/blob/master',
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
            'zh-CN',
            'zh-CN',
            'https://github.com/hunzhiwange/framework/blob/master',
        );
        $result = $doc->handleAndSave(Demo3::class, 'demo3_custom');
        $docFile = __DIR__.'/Assert/Doc/Doc/zh-CN/demo3_custom.md';
        $this->assertSame($result[0], $docFile);
        $this->assertFileExists($docFile);
        $this->assertSame(file_get_contents($docFile), $result[1]);
    }
}
