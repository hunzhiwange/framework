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

namespace Tests\Router\Console;

use Leevel\Di\IContainer;
use Leevel\Filesystem\Helper;
use Leevel\Router\Console\Controller;
use Tests\Console\BaseMake;
use Tests\TestCase;

class ControllerTest extends TestCase
{
    use BaseMake;

    public function testBaseUse(): void
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

        $result = $this->normalizeContent($result);
        $this->assertStringContainsString($this->normalizeContent('controller <BarValue> created successfully.'), $result);
        $this->assertStringContainsString('class BarValue', file_get_contents($file));
        unlink($file);
    }

    public function testActionSpecial(): void
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

        $result = $this->normalizeContent($result);
        $this->assertStringContainsString($this->normalizeContent('controller <Hello> created successfully.'), $result);
        $this->assertStringContainsString('class Hello', $content = file_get_contents($file));
        $this->assertStringContainsString('function helloWorldYes', $content);
        unlink($file);
    }

    public function testWithSubDir(): void
    {
        $dir = __DIR__.'/../../Console/Subdir';
        $file = $dir.'/Suddir2/BarValue.php';
        if (is_dir($dir)) {
            Helper::deleteDirectory($dir);
        }

        $result = $this->runCommand(new Controller(), [
            'command'     => 'make:controller',
            'name'        => 'BarValue',
            'action'      => 'hello',
            '--subdir'    => 'subdir/suddir2',
            '--namespace' => 'common',
        ], function ($container) {
            $this->initContainerService($container);
        });

        $result = $this->normalizeContent($result);
        $this->assertStringContainsString($this->normalizeContent('controller <BarValue> created successfully.'), $result);
        $this->assertStringContainsString($this->normalizeContent(realpath($file)), $result);
        $this->assertStringContainsString('class BarValue', file_get_contents($file));
        Helper::deleteDirectory($dir);
    }

    public function testWithCustomStub(): void
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
            '--stub'      => __DIR__.'/../assert/controller_stub',
        ], function ($container) {
            $this->initContainerService($container);
        });

        $result = $this->normalizeContent($result);
        $this->assertStringContainsString($this->normalizeContent('controller <BarValue> created successfully.'), $result);
        $this->assertStringContainsString('controller_stub', file_get_contents($file));
        unlink($file);
    }

    public function testWithCustomStubButNotFound(): void
    {
        $result = $this->runCommand(new Controller(), [
            'command'     => 'make:controller',
            'name'        => 'BarValue',
            'action'      => 'hello',
            '--namespace' => 'common',
            '--stub'      => '/notFound',
        ], function ($container) {
            $this->initContainerService($container);
        });

        $result = $this->normalizeContent($result);
        $this->assertStringContainsString($this->normalizeContent('Controller stub file `/notFound` was not found.'), $result);
    }

    protected function initContainerService(IContainer $container): void
    {
    }
}
