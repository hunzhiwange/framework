<?php

declare(strict_types=1);

namespace Tests\Database\Console;

use Leevel\Database\Console\Migrate;
use Leevel\Di\IContainer;
use Leevel\Kernel\IApp;
use Tests\Console\BaseCommand;
use Tests\TestCase;

class MigrateMigrate extends TestCase
{
    use BaseCommand;

    public function testBaseUse(): void
    {
        $result = $this->runCommand(
            new Migrate(),
            [
                'command' => 'migrate:migrate',
            ],
            function ($container) {
                $this->initContainerService($container);
            }
        );

        $result = $this->normalizeContent($result);

        $this->assertStringContainsString(
            $this->normalizeContent('using config file ./phinx.php'),
            $result
        );

        $this->assertStringContainsString(
            $this->normalizeContent('using config parser php'),
            $result
        );

        $this->assertStringContainsString(
            $this->normalizeContent('ordering by creation time'),
            $result
        );

        $this->assertStringContainsString(
            $this->normalizeContent('All Done. Took'),
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
