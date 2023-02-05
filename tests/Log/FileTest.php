<?php

declare(strict_types=1);

namespace Tests\Log;

use Leevel\Filesystem\Helper;
use Leevel\Log\File;
use Leevel\Log\ILog;
use Tests\TestCase;

/**
 * @internal
 *
 * @coversNothing
 */
final class FileTest extends TestCase
{
    protected function setUp(): void
    {
        $this->tearDown();
    }

    protected function tearDown(): void
    {
        $dirPath = [
            __DIR__.'/development.info',
        ];
        foreach ($dirPath as $v) {
            if (is_dir($v)) {
                Helper::deleteDirectory($v);
            }
        }
    }

    public function testBaseUse(): void
    {
        $file = new File([
            'path' => __DIR__,
        ]);

        $data = $this->getLogData();
        $file->info(...$data);
        $file->info(...$data);
        $file->flush();
        $filePath = __DIR__.'/development.info/'.ILOG::DEFAULT_MESSAGE_CATEGORY.'-'.date('Y-m-d').'.log';
        static::assertTrue(is_file($filePath));
    }

    public function testFileNotSetException(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Path for log has not set.');

        $file = new File();
        $data = $this->getLogData();
        $file->info(...$data);
        $file->flush($data);
    }

    protected function getLogData(): array
    {
        return [
            'hello',
            [
                'hello',
                'world',
            ],
        ];
    }
}
