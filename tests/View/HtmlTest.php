<?php

declare(strict_types=1);

namespace Tests\View;

use Leevel\Filesystem\Helper;
use Leevel\View\Compiler;
use Leevel\View\Html;
use Leevel\View\Parser;
use Tests\TestCase;

final class HtmlTest extends TestCase
{
    protected function tearDown(): void
    {
        Helper::deleteDirectory(__DIR__.'/cache_html');
    }

    public function testBaseUse(): void
    {
        $html = new Html([
            'theme_path' => __DIR__.'/assert',
            'cache_path' => __DIR__.'/cache_html',
        ]);

        $html->setParseResolver(function () {
            return $this->makeHtml();
        });

        $html->setVar('foo', 'bar');

        $result = $html->display('html_test');
        static::assertSame('hello html,bar.', $result);
    }

    public function testDisplayReturn(): void
    {
        $html = new Html([
            'theme_path' => __DIR__.'/assert',
            'cache_path' => __DIR__.'/cache_html',
        ]);

        $html->setParseResolver(function () {
            return $this->makeHtml();
        });

        $html->setVar('foo', 'bar');

        $result = $html->display('html_test');

        static::assertSame('hello html,bar.', $result);
    }

    public function testDisplayWithVar(): void
    {
        $html = new Html([
            'theme_path' => __DIR__.'/assert',
            'cache_path' => __DIR__.'/cache_html',
        ]);

        $html->setParseResolver(function () {
            return $this->makeHtml();
        });

        $result = $html->display('html_test', ['foo' => 'bar']);

        static::assertSame('hello html,bar.', $result);
    }

    public function testNotSetParseResolverException(): void
    {
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage(
            'Html theme not set parse resolver.'
        );

        $html = new Html([
            'theme_path' => __DIR__.'/assert',
            'cache_path' => __DIR__.'/cache_html',
        ]);

        $html->display('html_test', ['foo' => 'bar']);
    }

    public function testThemeCachePathIsNotSetException(): void
    {
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage(
            'Theme cache path must be set.'
        );

        $html = new Html([
            'theme_path' => __DIR__.'/assert',
        ]);

        $html->setParseResolver(function () {
            return $this->makeHtml();
        });

        $html->display('html_test', ['foo' => 'bar']);
    }

    public function testCacheLifetime(): void
    {
        $html = new Html([
            'theme_path' => __DIR__.'/assert',
            'cache_path' => __DIR__.'/cache_html',
        ]);

        $cachePath = $html->parseCachePath(__DIR__.'/assert/html_test_cachelisfetime.html');

        $html->setParseResolver(function () {
            return $this->makeHtml();
        });

        $html->setVar('foo', 'bar');

        static::assertFalse(is_file($cachePath));

        $result = $html->display('html_test_cachelisfetime');

        static::assertSame('hello html cachelifetime,bar.', $result);

        static::assertTrue(is_file($cachePath));
    }

    public function testCacheLifetime2(): void
    {
        $html = new Html([
            'theme_path' => __DIR__.'/assert',
            'cache_path' => __DIR__.'/cache_html',
        ]);

        $html->setParseResolver(function () {
            return $this->makeHtml();
        });

        $html->setVar('foo', 'bar');

        $cachePath = $html->parseCachePath(__DIR__.'/assert/html_test_cachelisfetime2.html');

        static::assertFalse(is_file($cachePath));

        $result = $html->display('html_test_cachelisfetime2');

        static::assertTrue(is_file($cachePath));

        static::assertSame('hello html cachelifetime2,bar.', $result);

        $result = $html->display('html_test_cachelisfetime2');

        static::assertTrue(is_file($cachePath));

        static::assertSame('hello html cachelifetime2,bar.', $result);
    }

    public function testCacheLifetime3(): void
    {
        $html = new Html([
            'theme_path' => __DIR__.'/assert',
            'cache_path' => __DIR__.'/cache_html',
        ]);

        $html->setParseResolver(function () {
            return $this->makeHtml();
        });

        $html->setVar('foo', 'bar');

        $cachePath = $html->parseCachePath(__DIR__.'/assert/html_test_cachelisfetime3.html');

        // 模板不存在，已过期
        static::assertFalse(is_file($cachePath));

        $result = $html->display('html_test_cachelisfetime3');

        static::assertTrue(is_file($cachePath));

        static::assertSame('hello html cachelifetime3,bar.', $result);
    }

    public function testCacheLifetime4(): void
    {
        $html = new Html([
            'theme_path' => __DIR__.'/assert',
            'cache_path' => __DIR__.'/cache_html',
        ]);

        $html->setParseResolver(function () {
            return $this->makeHtml();
        });

        $cachePath = $html->parseCachePath(__DIR__.'/assert/html_test_cachelisfetime4.html');

        $html->setVar('foo', 'bar');

        $file = __DIR__.'/assert/html_test_cachelisfetime4.html';

        file_put_contents($file, 'hello html cachelifetime4,{{ $foo }}.');

        sleep(1);

        static::assertFalse(is_file($cachePath));

        $result = $html->display('html_test_cachelisfetime4');

        static::assertTrue(is_file($cachePath));

        static::assertSame('hello html cachelifetime4,bar.', $result);

        static::assertFalse(filemtime($file) >= filemtime($cachePath));

        // 源文件已更新，过期
        file_put_contents($file, 'new for hello html cachelifetime4,{{ $foo }}.');

        static::assertTrue(filemtime($file) >= filemtime($cachePath));

        $result = $html->display('html_test_cachelisfetime4');

        static::assertSame('new for hello html cachelifetime4,bar.', $result);

        unlink($file);
    }

    protected function makeHtml(): Parser
    {
        return (new Parser(new Compiler()))
            ->registerCompilers()
            ->registerParsers()
        ;
    }
}
