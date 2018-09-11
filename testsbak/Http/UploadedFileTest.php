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

use Leevel\Http\UploadedFile;
use Tests\TestCase;

/**
 * UploadedFile test.
 * This class borrows heavily from the Symfony4 Framework and is part of the symfony package.
 *
 * @author Xiangmin Liu <635750556@qq.com>
 *
 * @since 2018.08.08
 *
 * @version 1.0
 *
 * @see Symfony\Component\HttpFoundation (https://github.com/symfony/symfony)
 */
class UploadedFileTest extends TestCase
{
    protected function setUp()
    {
        if (!ini_get('file_uploads')) {
            $this->markTestSkipped('file_uploads is disabled in php.ini.');
        }
    }

    public function testBaseUse()
    {
        $filePath = __DIR__.'/assert/source.txt';

        $file = new UploadedFile(
            $filePath,
            'foo.txt',
            null,
            UPLOAD_ERR_OK
        );

        $this->assertSame('application/octet-stream', $file->getMimeType());
        $this->assertSame('foo.txt', $file->getOriginalName());
        $this->assertSame('txt', $file->getOriginalExtension());
        $this->assertSame(UPLOAD_ERR_OK, $file->getError());
        $this->assertFalse($file->isValid());

        if (\extension_loaded('fileinfo')) {
            $this->assertSame('application/octet-stream', $file->getMimeType());
        }
    }

    public function testConstructWhenFileNotExists()
    {
        $filePath = __DIR__.'/assert/not_here';

        $this->expectException(\Leevel\Http\FileNotFoundException::class);
        $this->expectExceptionMessage($filePath);

        new UploadedFile($filePath, 'original.gif', null);
    }

    public function testGetMimeType()
    {
        $file = new UploadedFile(
            __DIR__.'/assert/source.txt',
            'foo.txt',
            'image/jpeg',
            null
        );

        $this->assertEquals('image/jpeg', $file->getMimeType());
    }

    public function testErrorIsOkByDefault()
    {
        $file = new UploadedFile(
            __DIR__.'/assert/source.txt',
            'foo.txt',
            null,
            null
        );

        $this->assertEquals(UPLOAD_ERR_OK, $file->getError());
    }

    public function testGetOriginalName()
    {
        $file = new UploadedFile(
            __DIR__.'/assert/source.txt',
            'foo.txt',
            null,
            null
        );

        $this->assertEquals('foo.txt', $file->getOriginalName());
    }

    public function testGetOriginalExtension()
    {
        $file = new UploadedFile(
            __DIR__.'/assert/source.txt',
            'foo.txt',
            null,
            null
        );

        $this->assertEquals('txt', $file->getOriginalExtension());
    }

    public function testMoveLocalFileIsNotAllowed()
    {
        $this->expectException(\Leevel\Http\FileException::class);
        $this->expectExceptionMessage(
            'The file foo.txt was not uploaded due to an unknown error.'
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
     * @dataProvider failedUploadedFile
     */
    public function testMoveFailed(UploadedFile $file)
    {
        switch ($file->getError()) {
            case UPLOAD_ERR_INI_SIZE:
                $exceptionMessage = sprintf('The file foo.txt exceeds your upload_max_filesize ini directive (limit is %d KiB).', UploadedFile::getMaxFilesize());

                break;
            case UPLOAD_ERR_FORM_SIZE:
                $exceptionMessage = 'The file foo.txt exceeds the upload limit defined in your form.';

                break;
            case UPLOAD_ERR_PARTIAL:
                $exceptionMessage = 'The file foo.txt was only partially uploaded.';

                break;
            case UPLOAD_ERR_NO_FILE:
                $exceptionMessage = 'No file was uploaded.';

                break;
            case UPLOAD_ERR_CANT_WRITE:
                $exceptionMessage = 'The file foo.txt could not be written on disk.';

                break;
            case UPLOAD_ERR_NO_TMP_DIR:
                $exceptionMessage = 'File could not be uploaded: missing temporary directory.';

                break;
            case UPLOAD_ERR_EXTENSION:
                $exceptionMessage = 'File upload was stopped by a PHP extension.';

                break;
            default:
                $exceptionMessage = 'The file foo.txt was not uploaded due to an unknown error.';
        }

        $this->expectException(\Leevel\Http\FileException::class);

        if (UPLOAD_ERR_INI_SIZE !== $file->getError()) {
            $this->expectExceptionMessage($exceptionMessage);
        }

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
                $error
            )];
        }
    }

    public function testMoveLocalFileIsAllowedInTestMode()
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

    public function testGetSize()
    {
        $filePath = __DIR__.'/assert/source.txt';

        $file = new UploadedFile(
            $filePath,
            'foo.txt',
            null
        );

        $this->assertEquals(filesize($filePath), $file->getSize());
    }

    public function testIsValid()
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
     * @param int $error
     */
    public function testIsInvalidOnUploadError(int $error)
    {
        $file = new UploadedFile(
            __DIR__.'/assert/source.txt',
            'foo.txt',
            null,
            $error
        );

        $this->assertFalse($file->isValid());
    }

    public function uploadedFileErrorProvider()
    {
        return [
            [UPLOAD_ERR_INI_SIZE],
            [UPLOAD_ERR_FORM_SIZE],
            [UPLOAD_ERR_PARTIAL],
            [UPLOAD_ERR_NO_TMP_DIR],
            [UPLOAD_ERR_EXTENSION],
        ];
    }

    public function testIsInvalidIfNotHttpUpload()
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
