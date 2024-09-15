<?php

declare(strict_types=1);

namespace Tests\I18n;

use Leevel\Filesystem\Helper\CreateDirectory;
use Leevel\Filesystem\Helper\DeleteDirectory;
use Leevel\I18n\GettextGenerator;
use Tests\TestCase;

final class GettextGeneratorTest extends TestCase
{
    #[\Override]
    protected function tearDown(): void
    {
        $path = __DIR__.'/cacheFile';
        if (is_dir($path)) {
            DeleteDirectory::handle($path);
        }

        if (isset($GLOBALS['RUNTIME_ERROR_REPORTING'])) {
            error_reporting($GLOBALS['RUNTIME_ERROR_REPORTING']);
            unset($GLOBALS['RUNTIME_ERROR_REPORTING']);
        }
    }

    public function test1(): void
    {
        $gettextGenerator = new GettextGenerator();
        $generatedLanguageFiles = $gettextGenerator->generatorPoFiles(
            [
                'base' => [__DIR__.'/assert/lang'],
            ],
            ['zh-CN', 'en-US'],
            __DIR__.'/cacheFile'
        );
        self::assertSame($generatedLanguageFiles, [
            'base' => [
                __DIR__.'/cacheFile/zh-CN/base.po',
                __DIR__.'/cacheFile/en-US/base.po',
            ],
        ]);

        $poContent = file_get_contents(__DIR__.'/cacheFile/zh-CN/base.po');
        self::assertStringContainsString('msgid "hello %s"', $poContent);
        self::assertStringContainsString('msgid "我的故事"', $poContent);
    }

    public function test3(): void
    {
        $gettextGenerator = new GettextGenerator();
        $generatedLanguageFiles = $gettextGenerator->generatorMoFiles(
            [
                'base' => [__DIR__.'/assert/lang'],
            ],
            ['zh-CN', 'en-US'],
            __DIR__.'/cacheFile'
        );
        self::assertSame($generatedLanguageFiles, [
            'base' => [
                __DIR__.'/cacheFile/zh-CN/base.mo',
                __DIR__.'/cacheFile/en-US/base.mo',
            ],
        ]);
    }

    public function test2(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage(
            'Languages must be array<string, array<string>>.'
        );

        $gettextGenerator = new GettextGenerator();
        $gettextGenerator->generatorPoFiles(
            [__DIR__.'/assert/lang'],
            ['zh-CN', 'en-US'],
            __DIR__.'/cacheFile'
        );
    }

    public function test4(): void
    {
        $filePath = __DIR__.'/cacheFile/zh-CN/base.po';

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage(
            sprintf('Failed to generate gettext file %s.', $filePath)
        );

        $GLOBALS['RUNTIME_ERROR_REPORTING'] = error_reporting();
        error_reporting(0);

        CreateDirectory::handle(\dirname($filePath));
        file_put_contents($filePath, 'foo');
        chmod($filePath, 0o444);

        $gettextGenerator = new GettextGenerator();
        $gettextGenerator->generatorPoFiles(
            [
                'base' => [__DIR__.'/assert/lang'],
            ],
            ['zh-CN', 'en-US'],
            __DIR__.'/cacheFile'
        );
    }
}
