<?php

declare(strict_types=1);

namespace Tests\Database\Console;

use Leevel\Database\Console\SeedRun;
use Tests\Console\BaseCommand;
use Tests\TestCase;

final class MigrateSeedRunTest extends TestCase
{
    use BaseCommand;

    public function testBaseUse(): void
    {
        $result = $this->runCommand(
            new SeedRun(),
            [
                'command' => 'migrate:seedrun',
            ],
            static function (): void {}
        );

        $result = $this->normalizeContent($result);

        self::assertStringContainsString(
            $this->normalizeContent('using config file'),
            $result
        );

        self::assertStringContainsString(
            $this->normalizeContent('migration.php'),
            $result
        );

        self::assertStringContainsString(
            $this->normalizeContent('Service databases was not found'),
            $result
        );
    }
}
