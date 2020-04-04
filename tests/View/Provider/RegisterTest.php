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
 * (c) 2010-2020 http://queryphp.com All rights reserved.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tests\View\Provider;

use Leevel\Di\Container;
use Leevel\Filesystem\Helper;
use Leevel\Kernel\App;
use Leevel\Option\Option;
use Leevel\View\Compiler;
use Leevel\View\Manager;
use Leevel\View\Parser;
use Leevel\View\Provider\Register;
use Tests\TestCase;

class RegisterTest extends TestCase
{
    protected function tearDown(): void
    {
        Helper::deleteDirectory(__DIR__.'/cache_theme', true);
    }

    public function testBaseUse(): void
    {
        $test = new Register($container = $this->createContainer());
        $test->register();
        $container->alias($test->providers());

        // view.views
        $manager = $container->make('view.views');
        $manager->setVar('foo', 'bar');
        $result = $manager->display('html_test');
        $this->assertSame('hello html,bar.', $result);

        // alias
        $manager = $container->make(Manager::class);
        $manager->setVar('foo', 'newbar');
        $result = $manager->display('html_test');
        $this->assertSame('hello html,newbar.', $result);

        // view.view
        $view = $container->make('view.view');
        $view->setVar('foo', 'newbarview');
        $result = $view->display('html_test');
        $this->assertSame('hello html,newbarview.', $result);
    }

    protected function createContainer(): Container
    {
        $app = new ExtendApp($container = new Container(), '');
        $container->instance('app', $app);

        $this->assertSame(__DIR__.'/assert', $app->themesPath());
        $this->assertSame(__DIR__.'/cache_theme', $app->runtimePath('theme'));

        $option = new Option([
            'view' => [
                'default'               => 'html',
                'action_fail'           => 'public/fail',
                'action_success'        => 'public/success',
                'connect'               => [
                    'html' => [
                        'driver'         => 'html',
                        'suffix'         => '.html',
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

        return $container;
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
