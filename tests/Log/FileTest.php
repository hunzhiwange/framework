<?php

declare(strict_types=1);

/*
 * This file is part of the ************************ package.
 * _____________                           _______________
 *  ______/     \__  _____  ____  ______  / /_  _________
 *   ____/ __   / / / / _ \/ __`\/ / __ \/ __ \/ __ \___
 *    __/ / /  / /_/ /  __/ /  \  / /_/ / / / / /_/ /__
 *      \_\ \_/\____/\___/_/   / / .___/_/ /_/ .___/
 *         \_\                /_/_/         /_/
 *
 * The PHP Framework For Code Poem As Free As Wind. <Query Yet Simple>
 * (c) 2010-2020 http://queryphp.com All rights reserved.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

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
                Helper::deleteDirectory($v, true);
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
                ILog::INFO,
                'hello',
                [
                    'hello',
                    'world',
                ],
            ],
        ];
    }
}
