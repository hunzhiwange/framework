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
use Leevel\Router\Console\Action;
use Tests\Console\BaseMake;
use Tests\TestCase;

/**
 * action test.
 *
 * @author Xiangmin Liu <635750556@qq.com>
 *
 * @since 2018.07.15
 *
 * @version 1.0
 */
class ActionTest extends TestCase
{
    use BaseMake;

    public function testBaseUse(): void
    {
        $file = __DIR__.'/../../Console/BarValue/Hello.php';
        if (is_file($file)) {
            unlink($file);
        }

        $result = $this->runCommand(new Action(), [
            'command'     => 'make:action',
            'controller'  => 'BarValue',
            'name'        => 'hello',
            '--namespace' => 'Common',
        ], function ($container) {
            $this->initContainerService($container);
        });

        $this->assertStringContainsString('action <hello> created successfully.', $result);
        $this->assertStringContainsString('class Hello', file_get_contents($file));
        unlink($file);
        rmdir(dirname($file));
    }

    public function testActionSpecial(): void
    {
        $file = __DIR__.'/../../Console/Hello/HelloWorldYes.php';
        if (is_file($file)) {
            unlink($file);
        }

        $result = $this->runCommand(new Action(), [
            'command'     => 'make:action',
            'controller'  => 'Hello',
            'name'        => 'hello-world_Yes',
            '--namespace' => 'common',
        ], function ($container) {
            $this->initContainerService($container);
        });

        $this->assertStringContainsString('action <hello-world_Yes> created successfully.', $result);
        $this->assertStringContainsString('class HelloWorldYes', file_get_contents($file));
        $this->assertStringContainsString('function handle', file_get_contents($file));
        unlink($file);
        rmdir(dirname($file));
    }

    public function testWithCustomStub(): void
    {
        $file = __DIR__.'/../../Console/BarValue/Hello.php';
        if (is_file($file)) {
            unlink($file);
        }

        $result = $this->runCommand(new Action(), [
            'command'     => 'make:action',
            'controller'  => 'BarValue',
            'name'        => 'hello',
            '--namespace' => 'Common',
            '--stub'      => __DIR__.'/../assert/action_stub',
        ], function ($container) {
            $this->initContainerService($container);
        });

        $this->assertStringContainsString('action <hello> created successfully.', $result);
        $this->assertStringContainsString('action_stub', file_get_contents($file));
        unlink($file);
        rmdir(dirname($file));
    }

    public function testWithCustomStubButNotFound(): void
    {
        $result = $this->runCommand(new Action(), [
            'command'     => 'make:action',
            'controller'  => 'BarValue',
            'name'        => 'hello',
            '--namespace' => 'Common',
            '--stub'      => '/notFound',
        ], function ($container) {
            $this->initContainerService($container);
        });

        $this->assertStringContainsString('Action stub file `/notFound` was not found.', $result);
    }

    protected function initContainerService(IContainer $container): void
    {
    }
}
