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

namespace Tests\Http;

use Symfony\Component\HttpFoundation\File\UploadedFile;
use Tests\TestCase;

/**
 * - This class borrows heavily from the Symfony4 Framework and is part of the symfony package.
 *
 * @see Symfony\Component\HttpFoundation (https://github.com/symfony/symfony)
 *
 * @api(
 *     title="Uploaded File",
 *     path="component/http/uploadedfile",
 *     description="QueryPHP 的附件上传项统一包装为 `\Symfony\Component\HttpFoundation\File\UploadedFile` 对象进行处理。",
 * )
 */
class UploadedFileTest extends TestCase
{
    protected function setUp(): void
    {
        if (!ini_get('file_uploads')) {
            $this->markTestSkipped('file_uploads is disabled in php.ini.');
        }
    }

    /**
     * @api(
     *     title="基本使用方法",
     *     description="",
     *     note="",
     * )
     */
    public function testBaseUse(): void
    {
        $filePath = __DIR__.'/assert/source.txt';

        $file = new UploadedFile(
            $filePath,
            'foo.txt',
            null,
            UPLOAD_ERR_OK
        );

        $this->assertSame('text/plain', $file->getMimeType());
        $this->assertSame('foo.txt', $file->getClientOriginalName());
        $this->assertSame('txt', $file->getClientOriginalExtension());
        $this->assertSame(UPLOAD_ERR_OK, $file->getError());
        $this->assertFalse($file->isValid());

        if (\extension_loaded('fileinfo')) {
            $this->assertSame('text/plain', $file->getMimeType());
        }
    }

    public function testConstructWhenFileNotExists(): void
    {
        $filePath = __DIR__.'/assert/not_here';

        $this->expectException(\Symfony\Component\HttpFoundation\File\Exception\FileNotFoundException::class);
        $e = sprintf('The file "%s" does not exist', $filePath);
        $this->expectExceptionMessage($e);

        new UploadedFile($filePath, 'original.gif', null);
    }

    /**
     * @api(
     *     title="getMimeType 返回文件类型",
     *     description="",
     *     note="",
     * )
     */
    public function testGetMimeType(): void
    {
        $file = new UploadedFile(
            __DIR__.'/assert/source.txt',
            'foo.txt',
            'image/jpeg',
            null
        );

        $this->assertEquals('text/plain', $file->getMimeType());
    }

    /**
     * @api(
     *     title="getClientOriginalName 返回文件原始名字",
     *     description="",
     *     note="",
     * )
     */
    public function testGetClientOriginalName(): void
    {
        $file = new UploadedFile(
            __DIR__.'/assert/source.txt',
            'foo.txt',
            null,
            null
        );

        $this->assertEquals('foo.txt', $file->getClientOriginalName());
    }

    /**
     * @api(
     *     title="getClientOriginalExtension 返回文件原始名字扩展",
     *     description="",
     *     note="",
     * )
     */
    public function testGetClientOriginalExtension(): void
    {
        $file = new UploadedFile(
            __DIR__.'/assert/source.txt',
            'foo.txt',
            null,
            null
        );

        $this->assertEquals('txt', $file->getClientOriginalExtension());
    }

    /**
     * @api(
     *     title="getMimeType 返回文件类型",
     *     description="",
     *     note="",
     * )
     */
    public function testMoveLocalFileIsNotAllowed(): void
    {
        $this->expectException(\Symfony\Component\HttpFoundation\File\Exception\FileException::class);
        $this->expectExceptionMessage(
            'The file "foo.txt" was not uploaded due to an unknown error.'
        );

        $file = new UploadedFile(
            __DIR__.'/assert/source.txt',
            'foo.txt',
            null,
            UPLOAD_ERR_OK
        );

        $movedFile = $file->move(__DIR__.'/assert/target');
    }

    /**
     * @api(
     *     title="getError 返回上传错误",
     *     description="",
     *     note="",
     * )
     */
    public function testErrorIsOkByDefault(): void
    {
        $file = new UploadedFile(
            __DIR__.'/assert/source.txt',
            'foo.txt',
            null,
            null
        );

        $this->assertEquals(UPLOAD_ERR_OK, $file->getError());
    }

    /**
     * @dataProvider failedUploadedFile
     *
     * @api(
     *     title="getError 返回上传错误，错误类型例子",
     *     description="
     * **错误类型**
     *
     * ``` php
     * {[\Leevel\Kernel\Utils\Doc::getMethodBody(\Tests\Http\UploadedFileTest::class, 'failedUploadedFile')]}
     * ```
     * ",
     *     note="",
     * )
     */
    public function testMoveFailed(UploadedFile $file): void
    {
        // dump($file->getError());
        switch ($file->getError()) {
            case UPLOAD_ERR_INI_SIZE:
                $exceptionClass = \Symfony\Component\HttpFoundation\File\Exception\IniSizeFileException::class;

                break;
            case UPLOAD_ERR_FORM_SIZE:
                $exceptionClass = \Symfony\Component\HttpFoundation\File\Exception\FormSizeFileException::class;

                break;
            case UPLOAD_ERR_PARTIAL:
                $exceptionClass = \Symfony\Component\HttpFoundation\File\Exception\FileException::class;

                break;
            case UPLOAD_ERR_NO_FILE:
                $exceptionClass = \Symfony\Component\HttpFoundation\File\Exception\FileException::class;

                break;
            case UPLOAD_ERR_CANT_WRITE:
                $exceptionClass = \Symfony\Component\HttpFoundation\File\Exception\FileException::class;

                break;
            case UPLOAD_ERR_NO_TMP_DIR:
                $exceptionClass = \Symfony\Component\HttpFoundation\File\Exception\FileException::class;

                break;
            case UPLOAD_ERR_EXTENSION:
                $exceptionClass = \Symfony\Component\HttpFoundation\File\Exception\FileException::class;

                break;
            default:
                $exceptionClass = \Symfony\Component\HttpFoundation\File\Exception\FileException::class;
        }

        $this->expectException($exceptionClass);
        $file->move(__DIR__.'/assert/target');
    }

    public function failedUploadedFile()
    {
        foreach ([
            UPLOAD_ERR_INI_SIZE,
            UPLOAD_ERR_FORM_SIZE,
            UPLOAD_ERR_PARTIAL,
            UPLOAD_ERR_NO_FILE,
            UPLOAD_ERR_CANT_WRITE,
            UPLOAD_ERR_NO_TMP_DIR,
            UPLOAD_ERR_EXTENSION,
            -1,
        ] as $error) {
            yield [new UploadedFile(
                __DIR__.'/assert/source.txt',
                'foo.txt',
                null,
                $error,
                true,
            )];
        }
    }

    /**
     * @api(
     *     title="move 移动文件",
     *     description="",
     *     note="",
     * )
     */
    public function testMoveLocalFileIsAllowedInTestMode(): void
    {
        $sourcePath = __DIR__.'/assert/source.txt';
        $filePath = __DIR__.'/assert/test_uploadedtest.txt';
        $targetPath = __DIR__.'/assert/target/test_uploadedtest_target.txt';

        if (is_file($filePath)) {
            unlink($filePath);
        }

        if (is_file($targetPath)) {
            unlink($targetPath);
        }

        copy($sourcePath, $filePath);

        $file = new UploadedFile(
            $filePath,
            'test_uploadedtest_target.txt',
            null,
            UPLOAD_ERR_OK,
            true
        );

        $movedFile = $file->move(dirname($targetPath), 'test_uploadedtest_target.txt');

        $this->assertFileExists($targetPath);
        $this->assertFileNotExists($filePath);
        $this->assertEquals(realpath($targetPath), $movedFile->getRealPath());

        unlink($targetPath);
    }

    /**
     * @api(
     *     title="getSize 返回文件大小",
     *     description="",
     *     note="",
     * )
     */
    public function testGetSize(): void
    {
        $filePath = __DIR__.'/assert/source.txt';

        $file = new UploadedFile(
            $filePath,
            'foo.txt',
            null
        );

        $this->assertEquals(filesize($filePath), $file->getSize());
    }

    /**
     * @api(
     *     title="isValid 文件是否上传成功，成功例子",
     *     description="",
     *     note="命令行不能上传文件，第 **5** 个参数用于 mock 上传成功。",
     * )
     */
    public function testIsValid(): void
    {
        $file = new UploadedFile(
            __DIR__.'/assert/source.txt',
            'foo.txt',
            null,
            UPLOAD_ERR_OK,
            true
        );

        $this->assertTrue($file->isValid());
    }

    /**
     * @dataProvider uploadedFileErrorProvider
     *
     * @api(
     *     title="isValid 文件是否上传成功，失败例子",
     *     description="",
     *     note="
     * **错误类型**
     *
     * ``` php
     * {[\Leevel\Kernel\Utils\Doc::getMethodBody(\Tests\Http\UploadedFileTest::class, 'uploadedFileErrorProvider')]}
     * ```
     * ",
     * )
     */
    public function testIsInvalidOnUploadError(int $error): void
    {
        $file = new UploadedFile(
            __DIR__.'/assert/source.txt',
            'foo.txt',
            null,
            $error
        );

        $this->assertFalse($file->isValid());
    }

    public function uploadedFileErrorProvider(): array
    {
        return [
            [UPLOAD_ERR_INI_SIZE],
            [UPLOAD_ERR_FORM_SIZE],
            [UPLOAD_ERR_PARTIAL],
            [UPLOAD_ERR_NO_TMP_DIR],
            [UPLOAD_ERR_EXTENSION],
        ];
    }

    public function testIsInvalidIfNotHttpUpload(): void
    {
        $file = new UploadedFile(
            __DIR__.'/assert/source.txt',
            'foo.txt',
            null,
            UPLOAD_ERR_OK
        );

        $this->assertFalse($file->isValid());
    }
}
