<?php

declare(strict_types=1);

namespace Tests\Database\Console;

use Leevel\Database\Console\Migrate;
use Leevel\Database\Console\Rollback;
use Leevel\Di\IContainer;
use Leevel\Kernel\IApp;
use Tests\Console\BaseCommand;
use Tests\TestCase;

/**
 * @internal
 *
 * @coversNothing
 */
final class MigrateRollback extends TestCase
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

        // 恢复回滚
        $resultMigrate = $this->runCommand(
            new Migrate(),
            [
                'command' => 'migrate:migrate',
            ],
            function ($container): void {
                $this->initContainerService($container);
            }
        );

        static::assertStringContainsString(
            $this->normalizeContent('using config file ./phinx.php'),
            $result
        );

        static::assertStringContainsString(
            $this->normalizeContent('using config parser php'),
            $result
        );

        static::assertStringContainsString(
            $this->normalizeContent('ordering by creation time'),
            $result
        );

        static::assertStringContainsString(
            $this->normalizeContent('== 20200805012526 FieldAllowedNull: reverted'),
            $result
        );

        static::assertStringContainsString(
            $this->normalizeContent('All Done. Took'),
            $result
        );

        $resultMigrate = $this->normalizeContent($resultMigrate);

        static::assertStringContainsString(
            $this->normalizeContent('== 20200805012526 FieldAllowedNull: migrated'),
            $resultMigrate
        );

        static::assertStringContainsString(
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
