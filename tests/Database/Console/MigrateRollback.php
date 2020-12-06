<?php

declare(strict_types=1);

namespace Tests\Database\Console;

use Leevel\Database\Console\Migrate;
use Leevel\Database\Console\Rollback;
use Leevel\Di\IContainer;
use Leevel\Kernel\IApp;
use Tests\Console\BaseCommand;
use Tests\TestCase;

class MigrateRollback extends TestCase
{
    use BaseCommand;

    public function testBaseUse(): void
    {
        $result = $this->runCommand(
            new Rollback(),
            [
                'command' => 'migrate:rollback',
            ],
            function ($container) {
                $this->initContainerService($container);
            }
        );

        $result = $this->normalizeContent($result);

        // 恢复回滚
        $resultMigrate = $this->runCommand(
            new Migrate(),
            [
                'command' => 'migrate:migrate',
            ],
            function ($container) {
                $this->initContainerService($container);
            }
        );

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
            $this->normalizeContent('== 20200805012526 FieldAllowedNull: reverted'),
            $result
        );

        $this->assertStringContainsString(
            $this->normalizeContent('All Done. Took'),
            $result
        );

        $resultMigrate = $this->normalizeContent($resultMigrate);

        $this->assertStringContainsString(
            $this->normalizeContent('== 20200805012526 FieldAllowedNull: migrated'),
            $resultMigrate
        );

        $this->assertStringContainsString(
            $this->normalizeContent('All Done. Took'),
            $resultMigrate
        );
    }

    protected function initContainerService(IContainer $container): void
    {
        $app = $this->createMock(IApp::class);
        $this->assertInstanceof(IApp::class, $app);

        $container->singleton(IApp::class, $app);
    }
}
