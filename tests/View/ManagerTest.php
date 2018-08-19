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

use Leevel\Di\Container;
use Leevel\Di\IContainer;
use Leevel\Filesystem\Fso;
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
    protected function tearDown()
    {
        Fso::deleteDirectory(__DIR__.'/cache_theme', true);
    }

    public function testBaseUse()
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

    protected function createManager()
    {
        $container = new ExtendContainer();

        $manager = new Manager($container);

        $this->assertInstanceof(IContainer::class, $manager->container());
        $this->assertInstanceof(Container::class, $manager->container());

        $this->assertSame(__DIR__.'/assert/default', $container->pathTheme());
        $this->assertSame(__DIR__.'/cache_theme', $container->runtimePath('theme'));

        $option = new Option([
            'view' => [
                'default'               => 'html',
                'theme_name'            => 'default',
                'theme_path_default'    => '',
                'action_fail'           => 'public+fail',
                'action_success'        => 'public+success',
                'controlleraction_depr' => '/',

                'connect' => [
                    'html' => [
                        'driver'         => 'html',
                        'suffix'         => '.html',
                        'cache_lifetime' => 2592000,
                    ],
                ],
            ],
        ]);

        $container->singleton('option', $option);

        $request = new ExtendRequest();

        $this->assertSame('default_app', $request->app());
        $this->assertSame('default_controller', $request->controller());
        $this->assertSame('default_action', $request->action());

        $container->singleton('request', $request);

        $container->singleton('view.parser', function () {
            return $this->makeHtml();
        });

        return $manager;
    }

    protected function makeHtml()
    {
        return (new Parser(new Compiler()))->
            registerCompilers()->

            registerParsers();
    }
}

class ExtendContainer extends Container
{
    public function development()
    {
        return true;
    }

    public function pathTheme()
    {
        return __DIR__.'/assert/default';
    }

    public function runtimePath($type)
    {
        return __DIR__.'/cache_'.$type;
    }
}

class ExtendRequest
{
    public function app()
    {
        return 'default_app';
    }

    public function controller()
    {
        return 'default_controller';
    }

    public function action()
    {
        return 'default_action';
    }
}
