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
 * (c) 2010-2018 http://queryphp.com All rights reserved.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tests\Log;

use Leevel\Filesystem\Fso;
use Leevel\Log\File;
use Leevel\Log\ILog;
use Tests\TestCase;

/**
 * file test.
 *
 * @author Xiangmin Liu <635750556@qq.com>
 *
 * @since 2018.06.10
 *
 * @version 1.0
 */
class FileTest extends TestCase
{
    protected function tearDown()
    {
        // for testWriteException
        $path = __DIR__.'/write';

        if (is_dir($path)) {
            Fso::deleteDirectory($path, true);
        }

        // for testParentWriteException
        $path = __DIR__.'/parentWrite';

        if (is_dir($path)) {
            Fso::deleteDirectory($path, true);
        }

        // for testRenameLog
        $path = __DIR__.'/rename';

        if (is_dir($path)) {
            Fso::deleteDirectory($path, true);
        }
    }

    public function testBaseUse()
    {
        $file = new File([
            'path' => __DIR__,
        ]);

        $data = $this->getLogData();
        $file->flush($data);

        $filePath = __DIR__.'/development.info/'.date('Y-m-d H').'.log';
        $this->assertTrue(is_file($filePath));

        Fso::deleteDirectory(dirname($filePath), true);
    }

    public function testSetOption()
    {
        $file = new File();

        $file->setOption('path', __DIR__);

        $data = $this->getLogData();
        $file->flush($data);

        $filePath = __DIR__.'/development.info/'.date('Y-m-d H').'.log';
        $this->assertTrue(is_file($filePath));

        Fso::deleteDirectory(dirname($filePath), true);
    }

    public function testWriteException()
    {
        $path = __DIR__.'/write';

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage(sprintf('Dir %s is not writeable.', $path.'/development.info'));

        $file = new File([
            'path' => $path,
        ]);

        // 设置目录只读
        // 7 = 4+2+1 分别代表可读可写可执行
        mkdir($path, 0777);
        mkdir($path.'/development.info', 0444);

        if (is_writable($path.'/development.info')) {
            $this->markTestSkipped('Mkdir with chmod is invalid.');
        }

        $data = $this->getLogData();
        $file->flush($data);
    }

    public function testParentWriteException()
    {
        $path = __DIR__.'/parentWrite';

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage(sprintf('Unable to create the %s directory.', $path.'/development.info'));

        $file = new File([
            'path' => $path,
        ]);

        // 设置目录只读
        // 7 = 4+2+1 分别代表可读可写可执行
        mkdir($path, 0444);

        if (is_writable($path)) {
            $this->markTestSkipped('Mkdir with chmod is invalid.');
        }

        $data = $this->getLogData();
        $file->flush($data);
    }

    public function testRenameLog()
    {
        $path = __DIR__.'/rename';

        $file = new File([
            'name' => 'Y-m-d',
            'path' => $path,
            'size' => 60,
        ]);

        $data = $this->getLogData();

        // save
        $file->flush($data);
        $filePath = $path.'/development.info/'.date('Y-m-d').'.log';
        $this->assertFileExists($filePath);

        clearstatcache();

        // 很诡异的问题，time 与 filemtime 始终相差 39 秒
        // 开发的虚拟机中突然出现的问题，修改时区也无效
        if (time() < filemtime($filePath)) {
            $this->markTestSkipped('Time() < filemtime() is invalid.');
        }

        clearstatcache();
        $this->assertSame(52, filesize($filePath));
        $file->flush($data); // next > 50
        clearstatcache();
        $this->assertSame(104, filesize($filePath));

        sleep(2);
        $file->flush($data);
        $renameFilePath = $path.'/development.info/'.date('Y-m-d').'_2.log';
        $this->assertFileExists($renameFilePath);
        clearstatcache();
        $this->assertSame(104, filesize($renameFilePath));
    }

    public function testFileNotSetException()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Path for log has not set.');

        $file = new File();

        $data = $this->getLogData();
        $file->flush($data);
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
