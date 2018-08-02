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
use Leevel\View\Twig;
use Tests\TestCase;
use Twig_Environment;
use Twig_Loader_Filesystem;

/**
 * twig test.
 *
 * @author Xiangmin Liu <635750556@qq.com>
 *
 * @since 2018.08.02
 *
 * @version 1.0
 */
class TwigTest extends TestCase
{
    protected function tearDown()
    {
        Fso::deleteDirectory(__DIR__.'/cache', true);
    }

    public function testBaseUse()
    {
        $twig = new Twig([
            'theme_path' => __DIR__.'/assert/default',
        ]);

        $twig->setParseResolver(function () {
            return $this->makeTwig();
        });

        $twig->setVar('foo', 'bar');

        ob_start();

        $twig->display('twig_test');
        $result = ob_get_contents();

        ob_end_clean();

        $this->assertSame('hello twig,bar.', $result);

        $result = $twig->display('twig_test', [], null, false);

        $this->assertSame('hello twig,bar.', $result);
    }

    public function testDisplayReturn()
    {
        $twig = new Twig([
            'theme_path' => __DIR__.'/assert/default',
        ]);

        $twig->setParseResolver(function () {
            return $this->makeTwig();
        });

        $twig->setVar('foo', 'bar');

        $result = $twig->display('twig_test', [], null, false);

        $this->assertSame('hello twig,bar.', $result);
    }

    public function testDisplayWithVar()
    {
        $twig = new Twig([
            'theme_path' => __DIR__.'/assert/default',
        ]);

        $twig->setParseResolver(function () {
            return $this->makeTwig();
        });

        $result = $twig->display('twig_test', ['foo' => 'bar'], null, false);

        $this->assertSame('hello twig,bar.', $result);
    }

    public function testNotSetParseResolverException()
    {
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage(
            'Twig theme not set parse resolver.'
        );

        $twig = new Twig([
            'theme_path' => __DIR__.'/assert/default',
        ]);

        $twig->display('twig_test', ['foo' => 'bar'], null, false);
    }

    protected function makeTwig()
    {
        return new Twig_Environment(new Twig_Loader_Filesystem(), [
            'auto_reload' => true,
            'debug'       => true,
            'cache'       => __DIR__.'/cache',
        ]);
    }
}
