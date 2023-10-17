<?php

declare(strict_types=1);

namespace Tests\I18n;

use Leevel\Filesystem\Helper;
use Leevel\I18n\GettextGenerator;
use Tests\TestCase;

/**
 * @internal
 */
final class GettextGeneratorTest extends TestCase
{
    protected function tearDown(): void
    {
        $path = __DIR__.'/cacheFile';
        if (is_dir($path)) {
            Helper::deleteDirectory($path);
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
        static::assertSame($generatedLanguageFiles, [
            'base' => [
                __DIR__.'/cacheFile/zh-CN/base.po',
                __DIR__.'/cacheFile/en-US/base.po',
            ],
        ]);

        $poContent = file_get_contents(__DIR__.'/cacheFile/zh-CN/base.po');
        static::assertStringContainsString('msgid "hello %s"', $poContent);
        static::assertStringContainsString('msgid "我的故事"', $poContent);
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
        static::assertSame($generatedLanguageFiles, [
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
}
