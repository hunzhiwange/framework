<?php

declare(strict_types=1);

namespace Tests\Database\Console;

use Leevel\Database\Console\Rollback;
use Leevel\Di\IContainer;
use Leevel\Kernel\IApp;
use Tests\Console\BaseCommand;
use Tests\TestCase;

final class MigrateRollbackTest extends TestCase
{
    use BaseCommand;

    public function testBaseUse(): void
    {
        $result = $this->runCommand(
            new Rollback(),
            [
                'command' => 'migrate:rollback',
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
            $this->normalizeContent('using option parser php'),
            $result
        );

        static::assertStringContainsString(
            $this->normalizeContent('ordering by creation time'),
            $result
        );

        static::assertStringContainsString(
            $this->normalizeContent('reverting'),
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
