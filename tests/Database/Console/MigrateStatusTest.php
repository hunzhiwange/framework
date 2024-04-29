<?php

declare(strict_types=1);

namespace Tests\Database\Console;

use Leevel\Database\Console\Status;
use Leevel\Di\IContainer;
use Leevel\Kernel\IApp;
use Tests\Console\BaseCommand;
use Tests\TestCase;

final class MigrateStatusTest extends TestCase
{
    use BaseCommand;

    public function testBaseUse(): void
    {
        $result = $this->runCommand(
            new Status(),
            [
                'command' => 'migrate:status',
            ],
            function ($container): void {
                $this->initContainerService($container);
            }
        );

        $result = $this->normalizeContent($result);

        self::assertStringContainsString(
            $this->normalizeContent('using config file'),
            $result
        );

        self::assertStringContainsString(
            $this->normalizeContent('phinx.php'),
            $result
        );

        self::assertStringContainsString(
            $this->normalizeContent('using config parser php'),
            $result
        );

        self::assertStringContainsString(
            $this->normalizeContent('[Migration ID]'),
            $result
        );

        self::assertStringContainsString(
            $this->normalizeContent('Status'),
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
