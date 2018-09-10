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

use Leevel\Filesystem\Fso;
use Leevel\View\Compiler;
use Leevel\View\Html;
use Leevel\View\Parser;
use Tests\TestCase;

/**
 * html test.
 *
 * @author Xiangmin Liu <635750556@qq.com>
 *
 * @since 2018.08.03
 *
 * @version 1.0
 */
class HtmlTest extends TestCase
{
    protected function tearDown()
    {
        Fso::deleteDirectory(__DIR__.'/cache_html', true);
    }

    public function testBaseUse()
    {
        $html = new Html([
            'theme_path'       => __DIR__.'/assert',
            'cache_path'       => __DIR__.'/cache_html',
        ]);

        $html->setParseResolver(function () {
            return $this->makeHtml();
        });

        $html->setVar('foo', 'bar');

        ob_start();

        $html->display('html_test');
        $result = ob_get_contents();

        ob_end_clean();

        $this->assertSame('hello html,bar.', $result);

        $result = $html->display('html_test', [], null, false);

        $this->assertSame('hello html,bar.', $result);
    }

    public function testDisplayReturn()
    {
        $html = new Html([
            'theme_path'       => __DIR__.'/assert',
            'cache_path'       => __DIR__.'/cache_html',
        ]);

        $html->setParseResolver(function () {
            return $this->makeHtml();
        });

        $html->setVar('foo', 'bar');

        $result = $html->display('html_test', [], null, false);

        $this->assertSame('hello html,bar.', $result);
    }

    public function testDisplayWithVar()
    {
        $html = new Html([
            'theme_path'       => __DIR__.'/assert',
            'cache_path'       => __DIR__.'/cache_html',
        ]);

        $html->setParseResolver(function () {
            return $this->makeHtml();
        });

        $result = $html->display('html_test', ['foo' => 'bar'], null, false);

        $this->assertSame('hello html,bar.', $result);
    }

    public function testNotSetParseResolverException()
    {
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage(
            'Html theme not set parse resolver.'
        );

        $html = new Html([
            'theme_path'       => __DIR__.'/assert',
            'cache_path'       => __DIR__.'/cache_html',
        ]);

        $html->display('html_test', ['foo' => 'bar'], null, false);
    }

    public function testThemeCachePathIsNotSetException()
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

        $html->display('html_test', ['foo' => 'bar'], null, false);
    }

    public function testCacheLifetime()
    {
        $html = new Html([
            'theme_path'       => __DIR__.'/assert',
            'cache_path'       => __DIR__.'/cache_html',
        ]);

        $cachePath = $html->getCachePath(__DIR__.'/assert/html_test_cachelisfetime.html');

        $html->setParseResolver(function () {
            return $this->makeHtml();
        });

        $html->setVar('foo', 'bar');

        $this->assertFalse(is_file($cachePath));

        $result = $html->display('html_test_cachelisfetime', [], null, false);

        $this->assertSame('hello html cachelifetime,bar.', $result);

        $this->assertTrue(is_file($cachePath));
    }

    public function testCacheLifetime2()
    {
        $html = new Html([
            'theme_path'       => __DIR__.'/assert',
            'cache_path'       => __DIR__.'/cache_html',
        ]);

        $html->setParseResolver(function () {
            return $this->makeHtml();
        });

        $html->setVar('foo', 'bar');

        $cachePath = $html->getCachePath(__DIR__.'/assert/html_test_cachelisfetime2.html');

        $this->assertFalse(is_file($cachePath));

        $result = $html->display('html_test_cachelisfetime2', [], null, false);

        $this->assertTrue(is_file($cachePath));

        $this->assertSame('hello html cachelifetime2,bar.', $result);

        $result = $html->display('html_test_cachelisfetime2', [], null, false);

        $this->assertTrue(is_file($cachePath));

        $this->assertSame('hello html cachelifetime2,bar.', $result);
    }

    public function testCacheLifetime3()
    {
        $html = new Html([
            'theme_path'       => __DIR__.'/assert',
            'cache_path'       => __DIR__.'/cache_html',
        ]);

        $html->setParseResolver(function () {
            return $this->makeHtml();
        });

        $html->setVar('foo', 'bar');

        $cachePath = $html->getCachePath(__DIR__.'/assert/html_test_cachelisfetime3.html');

        // 模板不存在，已过期
        $this->assertFalse(is_file($cachePath));

        $result = $html->display('html_test_cachelisfetime3', [], null, false);

        $this->assertTrue(is_file($cachePath));

        $this->assertSame('hello html cachelifetime3,bar.', $result);
    }

    public function testCacheLifetime4()
    {
        $html = new Html([
            'theme_path'       => __DIR__.'/assert',
            'cache_path'       => __DIR__.'/cache_html',
        ]);

        $html->setParseResolver(function () {
            return $this->makeHtml();
        });

        $cachePath = $html->getCachePath(__DIR__.'/assert/html_test_cachelisfetime4.html');

        $html->setVar('foo', 'bar');

        $file = __DIR__.'/assert/html_test_cachelisfetime4.html';

        file_put_contents($file, 'hello html cachelifetime4,{$foo}.');

        sleep(1);

        $this->assertFalse(is_file($cachePath));

        $result = $html->display('html_test_cachelisfetime4', [], null, false);

        $this->assertTrue(is_file($cachePath));

        $this->assertSame('hello html cachelifetime4,bar.', $result);

        $this->assertFalse(filemtime($file) >= filemtime($cachePath));

        // 源文件已更新，过期
        file_put_contents($file, 'new for hello html cachelifetime4,{$foo}.');

        $this->assertTrue(filemtime($file) >= filemtime($cachePath));

        $result = $html->display('html_test_cachelisfetime4', [], null, false);

        $this->assertSame('new for hello html cachelifetime4,bar.', $result);

        unlink($file);
    }

    protected function makeHtml()
    {
        return (new Parser(new Compiler()))->
            registerCompilers()->

            registerParsers();
    }
}
