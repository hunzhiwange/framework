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

namespace Tests\Http;

use Leevel\Http\FileBag;
use Leevel\Http\UploadedFile;
use Tests\TestCase;

/**
 * FileBagTest test
 * This class borrows heavily from the Symfony4 Framework and is part of the symfony package.
 *
 * @author Xiangmin Liu <635750556@qq.com>
 *
 * @since 2018.03.25
 *
 * @version 1.0
 *
 * @see Symfony\Component\HttpFoundation (https://github.com/symfony/symfony)
 */
class FileBagTest extends TestCase
{
    protected function setUp()
    {
        $dir = sys_get_temp_dir().'/form_test';

        if (!is_dir($dir)) {
            mkdir($dir, 0777, true);
        }
    }

    protected function tearDown()
    {
        foreach (glob(sys_get_temp_dir().'/form_test/*') as $file) {
            unlink($file);
        }

        rmdir(sys_get_temp_dir().'/form_test');
    }

    public function testFileMustBeAnArrayOrUploadedFile()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage(
            'An uploaded file must be an array or an instance of UploadedFile.'
        );

        new FileBag(['file' => 'foo']);
    }

    public function testShouldConvertsUploadedFiles()
    {
        $tmpFile = $this->createTempFile();

        $file = new UploadedFile($tmpFile, basename($tmpFile), 'text/plain');

        $bag = new FileBag([
            'file' => [
                'name'     => basename($tmpFile),
                'type'     => 'text/plain',
                'tmp_name' => $tmpFile,
                'error'    => 0,
                'size'     => null,
            ], ]);

        $this->assertEquals($file, $bag->get('file'));
    }

    public function testShouldSetEmptyUploadedFilesToNull()
    {
        $bag = new FileBag([
            'file' => [
                'name'     => '',
                'type'     => '',
                'tmp_name' => '',
                'error'    => UPLOAD_ERR_NO_FILE,
                'size'     => 0,
            ], ]);

        $this->assertNull($bag->get('file'));
    }

    public function testShouldRemoveEmptyUploadedFilesForMultiUpload()
    {
        $bag = new FileBag([
            'files' => [
                'name'     => [''],
                'type'     => [''],
                'tmp_name' => [''],
                'error'    => [UPLOAD_ERR_NO_FILE],
                'size'     => [0],
            ],
        ]);

        $this->assertNull($bag->get('files'));
        $this->assertSame($bag->getArr('files'), []);
    }

    public function testShouldRemoveEmptyUploadedFilesForAssociativeArray()
    {
        $bag = new FileBag([
            'files' => [
                'name'     => ['file1' => ''],
                'type'     => ['file1' => ''],
                'tmp_name' => ['file1' => ''],
                'error'    => ['file1' => UPLOAD_ERR_NO_FILE],
                'size'     => ['file1' => 0],
            ], ]);

        $this->assertNull($bag->get('files'));
        $this->assertSame([], $bag->getArr('files'));
    }

    public function testShouldConvertUploadedFilesWithPhpBug()
    {
        $tmpFile = $this->createTempFile();
        $file = new UploadedFile($tmpFile, basename($tmpFile), 'text/plain');

        $bag = new FileBag([
            'child' => [
                'name' => [
                    'file' => basename($tmpFile),
                ],
                'type' => [
                    'file' => 'text/plain',
                ],
                'tmp_name' => [
                    'file' => $tmpFile,
                ],
                'error' => [
                    'file' => 0,
                ],
                'size' => [
                    'file' => null,
                ],
            ],
        ]);

        $files = $bag->all();
        $this->assertEquals($file, $files['child\file']);
    }

    public function testShouldConvertNestedUploadedFilesWithPhpBug()
    {
        $tmpFile = $this->createTempFile();
        $file = new UploadedFile($tmpFile, basename($tmpFile), 'text/plain');

        $bag = new FileBag([
            'child' => [
                'name' => [
                    'sub' => ['file' => basename($tmpFile)],
                ],
                'type' => [
                    'sub' => ['file' => 'text/plain'],
                ],
                'tmp_name' => [
                    'sub' => ['file' => $tmpFile],
                ],
                'error' => [
                    'sub' => ['file' => 0],
                ],
                'size' => [
                    'sub' => ['file' => null],
                ],
            ],
        ]);

        $files = $bag->all();

        $this->assertEquals($file, $files['child\sub\file']);
    }

    public function testShouldNotConvertNestedUploadedFiles()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage(
            'An uploaded file must be contain key name.'
        );

        $tmpFile = $this->createTempFile();
        $file = new UploadedFile($tmpFile, basename($tmpFile), 'text/plain');
        $bag = new FileBag(['image' => ['file' => $file]]);
    }

    public function testConvertUploadFileItem()
    {
        $tmpFile = $this->createTempFile();
        $file = new UploadedFile($tmpFile, basename($tmpFile), 'text/plain');
        $bag = new FileBag(['image' => $file]);

        $files = $bag->all();
        $this->assertSame($file, $files['image']);
    }

    public function testFileKeyIndexNotFoundException()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage(
            'An uploaded file must be contain sub in key size.'
        );

        $tmpFile = $this->createTempFile();
        $file = new UploadedFile($tmpFile, basename($tmpFile), 'text/plain');

        $bag = new FileBag([
            'child' => [
                'name' => [
                    'sub' => ['file' => basename($tmpFile)],
                ],
                'type' => [
                    'sub' => ['file' => 'text/plain'],
                ],
                'tmp_name' => [
                    'sub' => ['file' => $tmpFile],
                ],
                'error' => [
                    'sub' => ['file' => 0],
                ],
                'size' => [
                ],
            ],
        ]);
    }

    public function testFileKeyNotFoundException()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage(
            'An uploaded file must be contain key size.'
        );

        $tmpFile = $this->createTempFile();
        $file = new UploadedFile($tmpFile, basename($tmpFile), 'text/plain');

        $bag = new FileBag([
            'child' => [
                'name' => [
                    'sub' => ['file' => basename($tmpFile)],
                ],
                'type' => [
                    'sub' => ['file' => 'text/plain'],
                ],
                'tmp_name' => [
                    'sub' => ['file' => $tmpFile],
                ],
                'error' => [
                    'sub' => ['file' => 0],
                ],
            ],
        ]);
    }

    protected function createTempFile()
    {
        $tempFile = sys_get_temp_dir().'/form_test/'.md5(time().rand()).'.tmp';
        file_put_contents($tempFile, '1');

        return $tempFile;
    }
}
