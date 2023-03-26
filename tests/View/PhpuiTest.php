<?php

declare(strict_types=1);

namespace Tests\View;

use Leevel\View\Phpui;
use Tests\TestCase;

/**
 * @internal
 */
final class PhpuiTest extends TestCase
{
    public function testBaseUse(): void
    {
        $phpui = new Phpui([
            'theme_path' => __DIR__.'/assert',
        ]);

        $phpui->setVar('foo', 'bar');
        $result = $phpui->display('phpui_test');
        static::assertSame('hello phpui,bar.', $result);
    }

    public function testDisplayReturn(): void
    {
        $phpui = new Phpui([
            'theme_path' => __DIR__.'/assert',
        ]);

        $phpui->setVar('foo', 'bar');

        $result = $phpui->display('phpui_test');

        static::assertSame('hello phpui,bar.', $result);
    }

    public function testDisplayWithVar(): void
    {
        $phpui = new Phpui([
            'theme_path' => __DIR__.'/assert',
        ]);

        $result = $phpui->display('phpui_test', ['foo' => 'bar']);

        static::assertSame('hello phpui,bar.', $result);
    }

    public function testParseFileForFullPath(): void
    {
        $phpui = new Phpui([
            'theme_path' => __DIR__.'/assert',
        ]);

        $result = $phpui->display(__DIR__.'/assert/phpui_test_pullpath.php', ['foo' => 'bar']);

        static::assertSame('hello phpui for fullpath,bar.', $result);
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

    public function testParseFilePathNotFound(): void
    {
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage(
            sprintf('Template file `%s/phpui_test_not_found.php` does not exist.', $themePath = __DIR__.'/assert')
        );

        $phpui = new Phpui([
            'theme_path' => $themePath,
        ]);

        $phpui->display('phpui_test_not_found', ['foo' => 'bar']);
    }

    public function testParseFileForTheme(): void
    {
        $phpui = new Phpui([
            'theme_path' => __DIR__.'/assert',
        ]);

        $result = $phpui->display('sub/phpui_test_sub', ['foo' => 'bar']);

        static::assertSame('hello phpui for sub,bar.', $result);
    }

    public function testParseFilePathTemplateFileMustBeSet(): void
    {
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage(
            'Template file must be set.'
        );

        $phpui = new Phpui([
            'theme_path' => __DIR__.'/assert',
        ]);

        $phpui->display('', ['foo' => 'bar']);
    }

    public function testParseFilePathVar(): void
    {
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage(
            'Template file `hello_world` does not exist.'
        );

        $phpui = new Phpui([
            'theme_path' => __DIR__.'/assert',
        ]);

        $phpui->display('{"hello_world"}', ['foo' => 'bar']);
    }

    public function testParseFilePathVar2(): void
    {
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage(
            'Template file must be set.'
        );

        $phpui = new Phpui([
            'theme_path' => __DIR__.'/assert',
        ]);

        $phpui->display('{}', ['foo' => 'bar']);
    }

    public function testParseFilePathVar3(): void
    {
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage(
            'Eval [return hello;]: Undefined constant "hello"'
        );

        $phpui = new Phpui([
            'theme_path' => __DIR__.'/assert',
        ]);

        $phpui->display('{hello}', ['foo' => 'bar']);
    }
}
