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

namespace Tests\Router\Console;

use Leevel\Di\IContainer;
use Leevel\Kernel\IApp;
use Leevel\Router\Console\Controller;
use Leevel\Router\IRouter;
use Tests\Console\BaseMake;
use Tests\TestCase;

/**
 * controller test.
 *
 * @author Xiangmin Liu <635750556@qq.com>
 *
 * @since 2018.07.15
 *
 * @version 1.0
 */
class ControllerTest extends TestCase
{
    use BaseMake;

    public function testBaseUse()
    {
        $file = __DIR__.'/../../Console/BarValue.php';

        if (is_file($file)) {
            unlink($file);
        }

        $result = $this->runCommand(new Controller(), [
            'command'     => 'make:controller',
            'name'        => 'BarValue',
            'action'      => 'hello',
            '--namespace' => 'common',
        ], function ($container) {
            $this->initContainerService($container);
        });

        $this->assertStringContainsString('controller <BarValue> created successfully.', $result);

        $this->assertStringContainsString('class BarValue', file_get_contents($file));

        unlink($file);
    }

    public function testActionSpecial()
    {
        $file = __DIR__.'/../../Console/Hello.php';

        if (is_file($file)) {
            unlink($file);
        }

        $result = $this->runCommand(new Controller(), [
            'command'     => 'make:controller',
            'name'        => 'Hello',
            'action'      => 'hello-world_Yes',
            '--namespace' => 'common',
        ], function ($container) {
            $this->initContainerService($container);
        });

        $this->assertStringContainsString('controller <Hello> created successfully.', $result);

        $this->assertStringContainsString('class Hello', file_get_contents($file));

        $this->assertStringContainsString('function helloWorldYes', file_get_contents($file));

        unlink($file);
    }

    public function testExtend()
    {
        $file = __DIR__.'/../../Console/Hello.php';

        if (is_file($file)) {
            unlink($file);
        }

        $result = $this->runCommand(new Controller(), [
            'command'     => 'make:controller',
            'name'        => 'Hello',
            'action'      => 'hello-world_Yes',
            '--namespace' => 'common',
        ], function ($container) {
            $this->initContainerService($container);
        });

        $this->assertStringContainsString('controller <Hello> created successfully.', $result);

        $this->assertNotContains('class Hello extends Controller', file_get_contents($file));

        $this->assertStringContainsString('function helloWorldYes', file_get_contents($file));

        unlink($file);
    }

    protected function initContainerService(IContainer $container)
    {
        // 注册 app
        $app = $this->createMock(IApp::class);

        $this->assertInstanceof(IApp::class, $app);

        // 注册 router
        $router = $this->createMock(IRouter::class);

        $this->assertInstanceof(IRouter::class, $router);

        $router->method('getControllerDir')->willReturn('');
        $this->assertEquals('', $router->getControllerDir());

        $container->singleton(IRouter::class, $router);
    }
}
