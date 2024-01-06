<?php

declare(strict_types=1);

namespace Tests\Database\Console;

use Leevel\Database\Console\Create;
use Leevel\Di\IContainer;
use Leevel\Kernel\IApp;
use Tests\Console\BaseCommand;
use Tests\TestCase;

final class MigrateCreate extends TestCase
{
    use BaseCommand;

    public function testBaseUse(): void
    {
        $result = $this->runCommand(
            new Create(),
            [
                'command' => 'migrate:create',
                'name' => 'hello world',
            ],
            function ($container): void {
                $this->initContainerService($container);
            }
        );

        $result = $this->normalizeContent($result);

        static::assertStringContainsString(
            $this->normalizeContent('using config file'),
            $result
        );

        static::assertStringContainsString(
            $this->normalizeContent('phinx.php'),
            $result
        );

        static::assertStringContainsString(
            $this->normalizeContent('using config parser php'),
            $result
        );

        static::assertStringContainsString(
            $this->normalizeContent('The migration class name "hello world" is invalid. Please use CamelCase format.'),
            $result
        );

        static::assertStringContainsString(
            $this->normalizeContent('migrate:create'),
            $result
        );
    }

    protected function initContainerService(IContainer $container): void
    {
        $app = $this->createMock(IApp::class);
        $this->assertInstanceof(IApp::class, $app);

        $container->singleton(IApp::class, $app);
    }
}
