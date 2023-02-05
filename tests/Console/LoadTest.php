<?php

declare(strict_types=1);

namespace Tests\Console;

use Leevel\Console\Load;
use Tests\TestCase;

/**
 * @internal
 *
 * @coversNothing
 */
final class LoadTest extends TestCase
{
    public function testBaseUse(): void
    {
        $load = new Load();

        $load->addNamespace([
            'Tests\\Console\\Load1' => __DIR__.'/Load1',
            'Tests\\Console\\Load2' => __DIR__.'/Load2',
        ]);

        $data = $load->loadData();

        static::assertSame([
            'Tests\\Console\\Load1\\Test1',
            'Tests\\Console\\Load2\\Test1',
            'Tests\\Console\\Load2\\Test2',
        ], $data);

        $data = $load->loadData();

        static::assertSame([
            'Tests\\Console\\Load1\\Test1',
            'Tests\\Console\\Load2\\Test1',
            'Tests\\Console\\Load2\\Test2',
        ], $data);
    }

    public function testConsoleDirNotFound(): void
    {
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage(sprintf('Console load dir %s is not exits.', __DIR__.'/LoadNotFound'));

        $load = new Load();

        $load->addNamespace([
            'Tests\\Console\\Load1' => __DIR__.'/LoadNotFound',
        ]);

        $load->loadData();
    }
}
