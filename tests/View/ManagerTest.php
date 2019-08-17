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
 * (c) 2010-2019 http://queryphp.com All rights reserved.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tests\View;

use Leevel\Di\Container;
use Leevel\Di\IContainer;
use Leevel\Filesystem\Fso;
use Leevel\Kernel\App;
use Leevel\Option\Option;
use Leevel\View\Compiler;
use Leevel\View\Manager;
use Leevel\View\Parser;
use Tests\TestCase;

/**
 * manager test.
 *
 * @author Xiangmin Liu <635750556@qq.com>
 *
 * @since 2018.08.03
 *
 * @version 1.0
 */
class ManagerTest extends TestCase
{
    protected function tearDown(): void
    {
        Fso::deleteDirectory(__DIR__.'/cache_theme', true);
    }

    public function testBaseUse(): void
    {
        $manager = $this->createManager();

        $manager->setVar('foo', 'bar');

        ob_start();

        $manager->display('html_test');
        $result = ob_get_contents();

        ob_end_clean();

        $this->assertSame('hello html,bar.', $result);

        $result = $manager->display('html_test', [], null, false);

        $this->assertSame('hello html,bar.', $result);
    }

    public function testPhpui(): void
    {
        $manager = $this->createManager('phpui');

        $manager->setVar('foo', 'bar');

        ob_start();

        $manager->display('html_test');
        $result = ob_get_contents();

        ob_end_clean();

        $this->assertSame('hello html,bar.', $result);

        $result = $manager->display('html_test', [], null, false);

        $this->assertSame('hello html,bar.', $result);
    }

    protected function createManager(string $connect = 'html'): Manager
    {
        $app = new ExtendApp($container = new Container(), '');
        $container->instance('app', $app);

        $manager = new Manager($container);

        $this->assertInstanceof(IContainer::class, $manager->container());
        $this->assertInstanceof(Container::class, $manager->container());

        $this->assertSame(__DIR__.'/assert', $app->themesPath());
        $this->assertSame(__DIR__.'/cache_theme', $app->runtimePath('theme'));

        $option = new Option([
            'view' => [
                'default'               => $connect,
                'action_fail'           => 'public/fail',
                'action_success'        => 'public/success',
                'connect'               => [
                    'html' => [
                        'driver'         => 'html',
                        'suffix'         => '.html',
                    ],
                    'phpui' => [
                        'driver' => 'phpui',
                        'suffix' => '.php',
                    ],
                ],
            ],
        ]);

        $container->singleton('option', $option);

        $request = new ExtendRequest();

        $container->singleton('request', $request);

        $container->singleton('view.parser', function () {
            return $this->makeHtml();
        });

        return $manager;
    }

    protected function makeHtml(): Parser
    {
        return (new Parser(new Compiler()))
            ->registerCompilers()
            ->registerParsers();
    }
}

class ExtendApp extends App
{
    public function development(): bool
    {
        return true;
    }

    public function themesPath(string $path = ''): string
    {
        return __DIR__.'/assert';
    }

    public function runtimePath(string $path = ''): string
    {
        return __DIR__.'/cache_'.$path;
    }
}

class ExtendRequest
{
}
