<?php

declare(strict_types=1);

namespace Tests\Log;

use Leevel\Filesystem\Helper;
use Leevel\Log\File;
use Leevel\Log\ILog;
use Tests\TestCase;

class FileTest extends TestCase
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
        $file->store($data);
        $file->store($data);
        $filePath = __DIR__.'/development.info/'.date('Y-m-d H').'.log';
        $this->assertTrue(is_file($filePath));
    }

    public function testFileNotSetException(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Path for log has not set.');

        $file = new File();
        $data = $this->getLogData();
        $file->store($data);
    }

    protected function getLogData(): array
    {
        return [
            [
                ILog::LEVEL_INFO,
                'hello',
                [
                    'hello',
                    'world',
                ],
            ],
        ];
    }
}
