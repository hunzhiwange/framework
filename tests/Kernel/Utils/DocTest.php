<?php

declare(strict_types=1);

namespace Tests\Kernel\Utils;

use Leevel\Filesystem\Helper;
use Leevel\Kernel\Utils\Doc;
use Tests\Kernel\Utils\Assert\Demo5;
use Tests\Kernel\Utils\Assert\Demo6;
use Tests\Kernel\Utils\Assert\Demo7;
use Tests\Kernel\Utils\Assert\Demo8;
use Tests\Kernel\Utils\Assert\Doc\Demo1;
use Tests\Kernel\Utils\Assert\Doc\Demo2;
use Tests\Kernel\Utils\Assert\Doc\Demo3;
use Tests\Kernel\Utils\Assert\Doc\Demo4;
use Tests\TestCase;

final class DocTest extends TestCase
{
    protected function setUp(): void
    {
        $dirs = [
            __DIR__.'/Assert/Doc/Doc',
            __DIR__.'/Assert/Doc/cache',
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
        static::assertSame(file_get_contents(__DIR__.'/Assert/Doc/Demo1.md'), $result);
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
        static::assertSame(file_get_contents(__DIR__.'/Assert/Doc/Demo2.md'), $result);
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
        static::assertSame(file_get_contents(__DIR__.'/Assert/Doc/Demo3.md'), $result);
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
        static::assertSame($result[0], $docFile);
        static::assertFileExists($docFile);
        static::assertSame(file_get_contents($docFile), $result[1]);
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
        static::assertSame($result[0], $docFile);
        static::assertFileExists($docFile);
        static::assertSame(file_get_contents($docFile), $result[1]);
    }

    public function test1(): void
    {
        $doc = new Doc(
            __DIR__.'/Assert/Doc/{i18n}',
            'zh-CN',
            'zh-CN',
            'https://github.com/hunzhiwange/framework/blob/master',
        );
        $result = $doc->handle(Demo4::class);
        static::assertSame(file_get_contents(__DIR__.'/Assert/Doc/Demo4.md'), $result);
    }

    public function test2(): void
    {
        $doc = new Doc(
            __DIR__.'/Assert/Doc/{i18n}',
            'zh-CN',
            'zh-CN',
            'https://github.com/hunzhiwange/framework/blob/master',
        );
        $result = $doc->handle(\stdClass::class);
        static::assertSame('', $result);
    }

    public function test3(): void
    {
        $doc = new Doc(
            __DIR__.'/Assert/Doc/{i18n}',
            'zh-CN',
            'zh-CN',
            'https://github.com/hunzhiwange/framework/blob/master',
        );
        $result = $doc->handle(Demo5::class);
        static::assertSame('', $result);
    }

    public function test4(): void
    {
        $doc = new Doc(
            __DIR__.'/Assert/Doc/{i18n}',
            'zh-CN',
            'zh-CN',
            'https://github.com/hunzhiwange/framework/blob/master',
        );
        $result = $doc->handleAndSave(Demo5::class);
        static::assertFalse($result);
    }

    public function test5(): void
    {
        $result = Doc::getMethodBody(Demo5::class, 'doc1');
        $resultNew = <<<'eot'
# Tests\Kernel\Utils\Assert\Demo5::doc1
public function doc1(): void
{
}
eot;
        static::assertSame($resultNew, $result);
    }

    public function test6(): void
    {
        $result = Doc::getMethodBody(\stdClass::class, 'foo');
        static::assertSame('', $result);
    }

    public function test7(): void
    {
        $result = Doc::getClassBody(Demo5::class);
        $resultNew = <<<'eot'
namespace Tests\Kernel\Utils\Assert;

class Demo5
{
    public function __doc2(): void
    {
    }

    public function doc1(): void
    {
    }

    protected function doc3(): void
    {
    }
}
eot;
        static::assertSame($resultNew, $result);
    }

    public function test8(): void
    {
        $result = Doc::getClassBody(\stdClass::class);
        static::assertSame('', $result);
    }

    public function test9(): void
    {
        $result = Doc::getClassBody(Demo6::class);
        $resultNew = <<<'eot'
namespace Tests\Kernel\Utils\Assert;

use Leevel\Kernel\App;

class Demo6
{
    public function doc1(): string
    {
        return App::class;
    }
}
eot;
        static::assertSame($resultNew, $result);
    }

    public function test10(): void
    {
        $doc = new Doc(
            __DIR__.'/Assert/Doc/{i18n}',
            'zh-CN',
            'zh-CN',
            'https://github.com/hunzhiwange/framework/blob/master',
        );
        $result = $doc->handle(Demo7::class);
        $resultNew = <<<'eot'
# demo7

::: tip Testing Is Documentation
[tests/Kernel/Utils/Assert/Demo7.php](https://github.com/hunzhiwange/framework/blob/master/tests/Kernel/Utils/Assert/Demo7.php)
:::

demo doc
just test

**Uses**

``` php
<?php

use Leevel\Kernel\App;
use Leevel\Kernel\Utils\Api;
```

## title1

hello
world

::: tip
note
:::


eot;
        static::assertSame($resultNew, $result);
    }

    public function test11(): void
    {
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage(
            'Documentation error was found and error message is syntax error, unexpected token "echo"'
        );

        $doc = new Doc(
            __DIR__.'/Assert/Doc/{i18n}',
            'zh-CN',
            'zh-CN',
            'https://github.com/hunzhiwange/framework/blob/master',
        );
        $doc->handle(Demo8::class);
    }

    public function test12(): void
    {
        $logCacheDir = __DIR__.'/Assert/Doc/cache';
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage(
            sprintf('Documentation error was found and report at %s/errors/Tests/Kernel/Utils/Assert/Demo8/doc1.php and error message is syntax error, unexpected token "echo".', $logCacheDir)
        );

        $doc = new Doc(
            __DIR__.'/Assert/Doc/{i18n}',
            'zh-CN',
            'zh-CN',
            'https://github.com/hunzhiwange/framework/blob/master',
        );
        $doc->setLogPath($logCacheDir);
        $doc->handle(Demo8::class);
    }
}
