<?php

declare(strict_types=1);

namespace Tests\Console;

use Leevel\Console\Make;
use Leevel\Filesystem\Helper;
use Tests\Console\Command\MakeFile;
use Tests\Console\Command\MakeFileWithGlobalReplace;
use Tests\TestCase;

/**
 * @api(
 *     zh-CN:title="通用代码生成器基类",
 *     path="component/console/makecommand",
 *     zh-CN:description="
 * 在项目实际开发中经常需要生成一个基础模板，QueryPHP 对这一场景进行了封装，提供了一个基础的代码生成器基类，
 * 可以十分便捷地生成你需要的模板代码。
 * ",
 * )
 */
class MakeTest extends TestCase
{
    use BaseMake;

    protected function setUp(): void
    {
        $dirs = [
            __DIR__.'/Command/cache',
        ];
        foreach ($dirs as $dir) {
            if (is_dir($dir)) {
                Helper::deleteDirectory($dir);
            }
        }
    }

    /**
     * @api(
     *     zh-CN:title="基本使用方法",
     *     zh-CN:description="
     * **fixture 定义**
     *
     * **Tests\Console\Command\MakeFile**
     *
     * ``` php
     * {[\Leevel\Kernel\Utils\Doc::getClassBody(\Tests\Console\Command\MakeFile::class)]}
     * ```
     *
     * **tests/Console/Command/template**
     *
     * ``` html
     * {[file_get_contents('vendor/hunzhiwange/framework/tests/Console/Command/template')]}
     * ```
     * ",
     *     zh-CN:note="",
     * )
     */
    public function testBaseUse(): void
    {
        $result = $this->runCommand(new MakeFile(), [
            'command'     => 'make:test',
            'name'        => 'test',
        ]);

        $result = $this->normalizeContent($result);

        $this->assertStringContainsString($this->normalizeContent('test <test> created successfully.'), $result);

        $file = __DIR__.'/Command/cache/test';

        $this->assertStringContainsString('hello make file', $content = file_get_contents($file));

        $this->assertStringContainsString('hello key1', $content);
        $this->assertStringContainsString('hello key2', $content);
        $this->assertStringContainsString('hello key3', $content);
        $this->assertStringContainsString('hello key4', $content);

        unlink($file);
        rmdir(dirname($file));
    }

    public function testFileAleadyExists(): void
    {
        $file = __DIR__.'/Command/cache/test2';
        $dirname = dirname($file);
        mkdir($dirname, 0777, true);
        file_put_contents($file, 'foo');

        $result = $this->runCommand(new MakeFile(), [
            'command'     => 'make:test',
            'name'        => 'test2',
        ]);

        $result = $this->normalizeContent($result);

        $this->assertStringContainsString($this->normalizeContent('File is already exits.'), $result);

        unlink($file);
        rmdir($dirname);
    }

    public function testTemplateNotFound(): void
    {
        $file = __DIR__.'/Command/cache/test4';
        $dirname = dirname($file);

        $result = $this->runCommand(new MakeFile(), [
            'command'         => 'make:test',
            'name'            => 'test4',
            'template'        => 'notFound',
        ]);

        $result = $this->normalizeContent($result);

        $this->assertStringContainsString($this->normalizeContent('Stub not found.'), $result);
    }

    public function testMakeFileWithGlobalReplace(): void
    {
        $result = $this->runCommand(new MakeFileWithGlobalReplace(), [
            'command'     => 'makewithglobal:test',
            'name'        => 'test',
        ]);

        $result = $this->normalizeContent($result);

        $this->assertStringContainsString($this->normalizeContent('test <test> created successfully.'), $result);

        $file = __DIR__.'/Command/cache/test';

        $this->assertStringContainsString('hello make file', $content = file_get_contents($file));

        $this->assertStringContainsString('hello key1', $content);
        $this->assertStringContainsString('hello key2 global', $content);
        $this->assertStringContainsString('hello key3', $content);
        $this->assertStringContainsString('hello key4', $content);

        $this->assertSame([], Make::getGlobalReplace());

        unlink($file);
        rmdir(dirname($file));
    }
}
