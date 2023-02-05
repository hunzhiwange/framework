<?php

declare(strict_types=1);

namespace Tests\Router\Console;

use Leevel\Di\IContainer;
use Leevel\Router\Console\Action;
use Tests\Console\BaseMake;
use Tests\TestCase;

/**
 * @internal
 *
 * @coversNothing
 */
final class ActionTest extends TestCase
{
    use BaseMake;

    public function testBaseUse(): void
    {
        $file = __DIR__.'/../../Console/BarValue/Hello.php';
        if (is_file($file)) {
            unlink($file);
        }

        $result = $this->runCommand(new Action(), [
            'command' => 'make:action',
            'controller' => 'BarValue',
            'name' => 'hello',
            '--namespace' => 'Common',
        ], function ($container): void {
            $this->initContainerService($container);
        });

        $result = $this->normalizeContent($result);
        static::assertStringContainsString($this->normalizeContent('action <hello> created successfully.'), $result);
        static::assertStringContainsString('class Hello', file_get_contents($file));
        unlink($file);
        rmdir(\dirname($file));
    }

    public function testActionSpecial(): void
    {
        $file = __DIR__.'/../../Console/Hello/HelloWorldYes.php';
        if (is_file($file)) {
            unlink($file);
        }

        $result = $this->runCommand(new Action(), [
            'command' => 'make:action',
            'controller' => 'Hello',
            'name' => 'hello-world_Yes',
            '--namespace' => 'common',
        ], function ($container): void {
            $this->initContainerService($container);
        });

        $result = $this->normalizeContent($result);
        static::assertStringContainsString($this->normalizeContent('action <hello-world_Yes> created successfully.'), $result);
        static::assertStringContainsString('class HelloWorldYes', $content = file_get_contents($file));
        static::assertStringContainsString('function handle', $content);
        unlink($file);
        rmdir(\dirname($file));
    }

    public function testWithCustomStub(): void
    {
        $file = __DIR__.'/../../Console/BarValue/Hello.php';
        if (is_file($file)) {
            unlink($file);
        }

        $result = $this->runCommand(new Action(), [
            'command' => 'make:action',
            'controller' => 'BarValue',
            'name' => 'hello',
            '--namespace' => 'Common',
            '--stub' => __DIR__.'/../assert/action_stub',
        ], function ($container): void {
            $this->initContainerService($container);
        });

        $result = $this->normalizeContent($result);
        static::assertStringContainsString($this->normalizeContent('action <hello> created successfully.'), $result);
        static::assertStringContainsString('action_stub', file_get_contents($file));
        unlink($file);
        rmdir(\dirname($file));
    }

    public function testWithCustomStubButNotFound(): void
    {
        $result = $this->runCommand(new Action(), [
            'command' => 'make:action',
            'controller' => 'BarValue',
            'name' => 'hello',
            '--namespace' => 'Common',
            '--stub' => '/notFound',
        ], function ($container): void {
            $this->initContainerService($container);
        });

        $result = $this->normalizeContent($result);
        static::assertStringContainsString($this->normalizeContent('Action stub file `/notFound` was not found.'), $result);
    }

    protected function initContainerService(IContainer $container): void
    {
    }
}
