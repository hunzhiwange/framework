<?php

declare(strict_types=1);

namespace Tests\Database\Console;

use Leevel\Database\Console\SeedCreate;
use Leevel\Di\IContainer;
use Leevel\Kernel\IApp;
use Tests\Console\BaseCommand;
use Tests\TestCase;

final class MigrateSeedCreateTest extends TestCase
{
    use BaseCommand;

    #[\Override]
    protected function setUp(): void
    {
        $this->tearDown();
    }

    #[\Override]
    protected function tearDown(): void
    {
        $seedsFile = \dirname(__DIR__, 2).'/assets/database/seeds/HelloWorld.php';
        if (is_file($seedsFile)) {
            unlink($seedsFile);
        }
    }

    public function testBaseUse(): void
    {
        $result = $this->runCommand(
            new SeedCreate(),
            [
                'command' => 'migrate:seedcreate',
                'name' => 'HelloWorld',
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
            $this->normalizeContent('using seed base class Phinx\\Seed\\AbstractSeed'),
            $result
        );

        self::assertStringContainsString(
            $this->normalizeContent('created tests/assets/database/seeds/HelloWorld.php'),
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
