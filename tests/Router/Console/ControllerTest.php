<?php

declare(strict_types=1);

namespace Tests\Router\Console;

use Leevel\Di\IContainer;
use Leevel\Filesystem\Helper;
use Leevel\Router\Console\Controller;
use Tests\Console\BaseMake;
use Tests\TestCase;

/**
 * @internal
 *
 * @coversNothing
 */
final class ControllerTest extends TestCase
{
    use BaseMake;

    public function testBaseUse(): void
    {
        $file = __DIR__.'/../../Console/BarValue.php';
        if (is_file($file)) {
            unlink($file);
        }

        $result = $this->runCommand(new Controller(), [
            'command' => 'make:controller',
            'name' => 'BarValue',
            'action' => 'hello',
            '--namespace' => 'common',
        ], function ($container): void {
            $this->initContainerService($container);
        });

        $result = $this->normalizeContent($result);
        static::assertStringContainsString($this->normalizeContent('controller <BarValue> created successfully.'), $result);
        static::assertStringContainsString('class BarValue', file_get_contents($file));
        unlink($file);
    }

    public function testActionSpecial(): void
    {
        $file = __DIR__.'/../../Console/Hello.php';
        if (is_file($file)) {
            unlink($file);
        }

        $result = $this->runCommand(new Controller(), [
            'command' => 'make:controller',
            'name' => 'Hello',
            'action' => 'hello-world_Yes',
            '--namespace' => 'common',
        ], function ($container): void {
            $this->initContainerService($container);
        });

        $result = $this->normalizeContent($result);
        static::assertStringContainsString($this->normalizeContent('controller <Hello> created successfully.'), $result);
        static::assertStringContainsString('class Hello', $content = file_get_contents($file));
        static::assertStringContainsString('function helloWorldYes', $content);
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
            'command' => 'make:controller',
            'name' => 'BarValue',
            'action' => 'hello',
            '--subdir' => 'subdir/suddir2',
            '--namespace' => 'common',
        ], function ($container): void {
            $this->initContainerService($container);
        });

        $result = $this->normalizeContent($result);
        static::assertStringContainsString($this->normalizeContent('controller <BarValue> created successfully.'), $result);
        static::assertStringContainsString($this->normalizeContent(realpath($file)), $result);
        static::assertStringContainsString('class BarValue', file_get_contents($file));
        Helper::deleteDirectory($dir);
    }

    public function testWithCustomStub(): void
    {
        $file = __DIR__.'/../../Console/BarValue.php';
        if (is_file($file)) {
            unlink($file);
        }

        $result = $this->runCommand(new Controller(), [
            'command' => 'make:controller',
            'name' => 'BarValue',
            'action' => 'hello',
            '--namespace' => 'common',
            '--stub' => __DIR__.'/../assert/controller_stub',
        ], function ($container): void {
            $this->initContainerService($container);
        });

        $result = $this->normalizeContent($result);
        static::assertStringContainsString($this->normalizeContent('controller <BarValue> created successfully.'), $result);
        static::assertStringContainsString('controller_stub', file_get_contents($file));
        unlink($file);
    }

    public function testWithCustomStubButNotFound(): void
    {
        $result = $this->runCommand(new Controller(), [
            'command' => 'make:controller',
            'name' => 'BarValue',
            'action' => 'hello',
            '--namespace' => 'common',
            '--stub' => '/notFound',
        ], function ($container): void {
            $this->initContainerService($container);
        });

        $result = $this->normalizeContent($result);
        static::assertStringContainsString($this->normalizeContent('Controller stub file `/notFound` was not found.'), $result);
    }

    protected function initContainerService(IContainer $container): void
    {
    }
}
