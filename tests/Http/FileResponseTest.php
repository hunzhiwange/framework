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

use Leevel\Http\File;
use Leevel\Http\FileResponse;
use Leevel\Http\ResponseHeaderBag;
use SplFileInfo;
use SplFileObject;
use Tests\TestCase;

/**
 * - This class borrows heavily from the Symfony4 Framework and is part of the symfony package.
 *
 * @see Symfony\Component\HttpFoundation (https://github.com/symfony/symfony)
 *
 * @api(
 *     title="File Response",
 *     path="component/http/fileresponse",
 *     description="QueryPHP 针对文件可以直接返回一个 `\Leevel\Http\FileResponse` 响应对象。",
 * )
 */
class FileResponseTest extends TestCase
{
    protected function tearDown(): void
    {
        $files = [
            __DIR__.'/assert/setFileWithNotReadable_test.txt',
            __DIR__.'/assert/setFileWithNotReadable_test2.txt',
            __DIR__.'/assert/setContentNotNullException_test.txt',
            __DIR__.'/assert/setContentDispositionException_test.txt',
            __DIR__.'/assert/setContentFlow_test2.txt',
            __DIR__.'/assert/assert/fileresponse_test.txt',
        ];

        foreach ($files as $file) {
            if (is_file($file)) {
                unlink($file);
            }
        }
    }

    /**
     * @api(
     *     title="create 创建一个文件响应",
     *     description="",
     *     note="",
     * )
     */
    public function testConstruction(): void
    {
        $filePath = __DIR__.'/assert/fileresponse_test.txt';
        file_put_contents($filePath, 'foo');

        $response = new FileResponse($filePath, 404, ['X-Header' => 'Foo'], null, true, true);
        $this->assertSame(404, $response->getStatusCode());
        $this->assertSame('Foo', $response->headers->get('X-Header'));
        $this->assertTrue($response->headers->has('ETag'));
        $this->assertTrue($response->headers->has('Last-Modified'));
        $this->assertFalse($response->headers->has('Content-Disposition'));

        $response = FileResponse::create($filePath, 404, [], ResponseHeaderBag::DISPOSITION_INLINE);
        $this->assertSame(404, $response->getStatusCode());
        $this->assertFalse($response->headers->has('ETag'));
        $this->assertSame('inline; filename="fileresponse_test.txt"', $response->headers->get('Content-Disposition'));

        unlink($filePath);
    }

    /**
     * @api(
     *     title="从 \SplFileObject 创建一个文件响应",
     *     description="",
     *     note="",
     * )
     */
    public function testSetFileWithSplFileObject(): void
    {
        $filePath = __DIR__.'/assert/setFileWithSplFileObject_test.txt';
        file_put_contents($filePath, 'foo');

        $file = new SplFileObject($filePath);
        $response = new FileResponse($file, 404, ['X-Header' => 'Foo'], null, true, true);

        $this->assertSame(404, $response->getStatusCode());
        $this->assertSame('Foo', $response->headers->get('X-Header'));
        $this->assertTrue($response->headers->has('ETag'));
        $this->assertTrue($response->headers->has('Last-Modified'));
        $this->assertFalse($response->headers->has('Content-Disposition'));

        $response = FileResponse::create($filePath, 404, [], ResponseHeaderBag::DISPOSITION_INLINE);
        $this->assertSame(404, $response->getStatusCode());
        $this->assertFalse($response->headers->has('ETag'));
        $this->assertSame('inline; filename="setFileWithSplFileObject_test.txt"', $response->headers->get('Content-Disposition'));

        unlink($filePath);
    }

    /**
     * @api(
     *     title="从 \SplFileInfo 创建一个文件响应",
     *     description="",
     *     note="",
     * )
     */
    public function testSetFileWithSplFileInfo(): void
    {
        $filePath = __DIR__.'/assert/setFileWithSplFileInfo_test.txt';
        file_put_contents($filePath, 'foo');
        $file = new SplFileInfo($filePath);
        $response = new FileResponse($file, 404, ['X-Header' => 'Foo'], null, true, true);
        $this->assertSame(404, $response->getStatusCode());
        $this->assertSame('Foo', $response->headers->get('X-Header'));
        $this->assertTrue($response->headers->has('ETag'));
        $this->assertTrue($response->headers->has('Last-Modified'));
        $this->assertFalse($response->headers->has('Content-Disposition'));

        $response = FileResponse::create($filePath, 404, [], ResponseHeaderBag::DISPOSITION_INLINE);
        $this->assertSame(404, $response->getStatusCode());
        $this->assertFalse($response->headers->has('ETag'));
        $this->assertSame('inline; filename="setFileWithSplFileInfo_test.txt"', $response->headers->get('Content-Disposition'));

        unlink($filePath);
    }

    public function testSendContent(): void
    {
        $filePath = __DIR__.'/assert/setFileWithSplFileObject_test.txt';
        file_put_contents($filePath, 'foo');
        $file = new SplFileObject($filePath);
        $response = new FileResponse($file, 404, ['X-Header' => 'Foo'], null, true, true);

        $this->assertSame(404, $response->getStatusCode());
        $this->assertSame('Foo', $response->headers->get('X-Header'));
        $this->assertTrue($response->headers->has('ETag'));
        $this->assertTrue($response->headers->has('Last-Modified'));
        $this->assertFalse($response->headers->has('Content-Disposition'));

        $content = $this->obGetContents(function () use ($response): void {
            $response->sendContent();
        });
        $this->assertSame($content, '');

        unlink($filePath);
    }

    public function testSendContent2(): void
    {
        $filePath = __DIR__.'/assert/setFileWithSplFileObject_test.txt';
        file_put_contents($filePath, 'foo');
        $file = new SplFileObject($filePath);
        $response = new FileResponse($file, 200, ['X-Header' => 'Foo'], null, true, true);

        $this->assertSame(200, $response->getStatusCode());
        $this->assertSame('Foo', $response->headers->get('X-Header'));
        $this->assertTrue($response->headers->has('ETag'));
        $this->assertTrue($response->headers->has('Last-Modified'));
        $this->assertFalse($response->headers->has('Content-Disposition'));

        $content = $this->obGetContents(function () use ($response): void {
            $response->sendContent();
        });
        $this->assertSame($content, 'foo');

        unlink($filePath);
    }

    public function testSendContentFlow(): void
    {
        $condition = false;
        $filePath = __DIR__.'/assert/setFileWithSplFileObject_test.txt';
        file_put_contents($filePath, 'foo');
        $file = new SplFileObject($filePath);
        $response = new FileResponse($file, 200, ['X-Header' => 'Foo'], null, true, true);

        $this->assertSame(200, $response->getStatusCode());
        $this->assertSame('Foo', $response->headers->get('X-Header'));
        $this->assertTrue($response->headers->has('ETag'));
        $this->assertTrue($response->headers->has('Last-Modified'));
        $this->assertFalse($response->headers->has('Content-Disposition'));

        $content = $this->obGetContents(function () use ($response, $condition): void {
            $response
                ->if($condition)
                ->sendContent()
                ->else()
                ->if();
        });
        $this->assertSame($content, '');

        unlink($filePath);
    }

    public function testSendContentFlow2(): void
    {
        $condition = true;

        $filePath = __DIR__.'/assert/setFileWithSplFileObject_test.txt';
        file_put_contents($filePath, 'foo');
        $file = new SplFileObject($filePath);
        $response = new FileResponse($file, 200, ['X-Header' => 'Foo'], null, true, true);

        $this->assertSame(200, $response->getStatusCode());
        $this->assertSame('Foo', $response->headers->get('X-Header'));
        $this->assertTrue($response->headers->has('ETag'));
        $this->assertTrue($response->headers->has('Last-Modified'));
        $this->assertFalse($response->headers->has('Content-Disposition'));

        $content = $this->obGetContents(function () use ($response, $condition): void {
            $response
                ->if($condition)
                ->sendContent()
                ->else()
                ->if();
        });
        $this->assertSame($content, 'foo');

        unlink($filePath);
    }

    public function testSetFileWithNotReadable(): void
    {
        $filePath = __DIR__.'/assert/setFileWithNotReadable_test.txt';

        $this->expectException(\Leevel\Http\FileException::class);
        $this->expectExceptionMessage(
            'File must be readable.'
        );

        file_put_contents($filePath, 'foo');

        chmod($filePath, 0000);

        if (is_readable($filePath)) {
            $this->markTestSkipped('Chmod is invalid.');
        }

        new FileResponse($filePath);
    }

    public function testSetFileWithNotReadable2(): void
    {
        $filePath = __DIR__.'/assert/setFileWithNotReadable_test2.txt';

        $this->expectException(\Leevel\Http\FileException::class);
        $this->expectExceptionMessage(
            'File must be readable.'
        );

        file_put_contents($filePath, 'foo');

        $file = new File($filePath);

        chmod($filePath, 0000);

        if (is_readable($filePath)) {
            $this->markTestSkipped('Chmod is invalid.');
        }

        new FileResponse($file);
    }

    public function testSetContentNotNullException(): void
    {
        $filePath = __DIR__.'/assert/setContentNotNullException_test.txt';

        $this->expectException(\LogicException::class);
        $this->expectExceptionMessage(
            'The content cannot be set on a FileResponse instance.'
        );

        file_put_contents($filePath, 'foo');

        $file = new File($filePath);

        $response = new FileResponse($file);

        $response->setContent('not null');
    }

    public function testSetContentDispositionException(): void
    {
        $filePath = __DIR__.'/assert/setContentDispositionException_test.txt';

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage(
            'he disposition type is invalid.'
        );

        file_put_contents($filePath, 'foo');

        $file = new File($filePath);

        $response = new FileResponse($file);

        $response->setContentDisposition('not_supported');
    }

    public function testSetFileFlow(): void
    {
        $condition = false;

        $filePath = __DIR__.'/assert/setFileFlow_test.txt';
        $filePath2 = __DIR__.'/assert/setFileFlow_test2.txt';

        file_put_contents($filePath, 'foo');
        file_put_contents($filePath2, 'bar');

        $response = new FileResponse($filePath, 404, ['X-Header' => 'Foo'], null, true, true);

        $response
            ->if($condition)
            ->setFile($filePath)
            ->else()
            ->setFile($filePath2, 'inline', true)
            ->fi();

        $this->assertSame(404, $response->getStatusCode());
        $this->assertSame('Foo', $response->headers->get('X-Header'));
        $this->assertTrue($response->headers->has('ETag'));
        $this->assertTrue($response->headers->has('Last-Modified'));
        $this->assertTrue($response->headers->has('Content-Disposition'));
        $this->assertSame('inline; filename="setFileFlow_test2.txt"', $response->headers->get('Content-Disposition'));
        $this->assertInstanceOf(File::class, $file = $response->getFile());
        $this->assertSame($filePath2, $file->getPathname());
        $this->assertSame('setFileFlow_test2.txt', $file->getFilename());

        unlink($filePath);
        unlink($filePath2);
    }

    public function testSetAutoLastModifiedFlow(): void
    {
        $condition = false;

        $filePath = __DIR__.'/assert/setAutoLastModifiedFlow_test.txt';

        file_put_contents($filePath, 'foo');

        $response = new FileResponse($filePath, 404, ['X-Header' => 'Foo'], null, true, true);

        $response
            ->if($condition)
            ->setAutoLastModified()
            ->else()
            ->fi();

        $this->assertSame(404, $response->getStatusCode());
        $this->assertSame('Foo', $response->headers->get('X-Header'));
        $this->assertTrue($response->headers->has('ETag'));
        $this->assertTrue($response->headers->has('Last-Modified'));
        $this->assertFalse($response->headers->has('Content-Disposition'));

        unlink($filePath);
    }

    public function testSetAutoEtagFlow(): void
    {
        $condition = false;

        $filePath = __DIR__.'/assert/setAutoEtagFlow_test.txt';

        file_put_contents($filePath, 'foo');

        $response = new FileResponse($filePath, 404, ['X-Header' => 'Foo'], null, false, false);

        $response
            ->if($condition)
            ->setAutoEtag()
            ->else()
            ->fi();

        $this->assertSame(404, $response->getStatusCode());
        $this->assertSame('Foo', $response->headers->get('X-Header'));
        $this->assertFalse($response->headers->has('ETag'));
        $this->assertFalse($response->headers->has('Last-Modified'));
        $this->assertFalse($response->headers->has('Content-Disposition'));

        $response
            ->if($condition)
            ->else()
            ->setAutoEtag()
            ->fi();

        $this->assertSame(
            base64_encode(hash_file('sha256', $filePath, true)),
            $response->headers->get('ETag')
        );

        unlink($filePath);
    }

    public function testSetContentFlow(): void
    {
        $condition = false;

        $filePath = __DIR__.'/assert/setContentFlow_test.txt';

        file_put_contents($filePath, 'foo');

        $response = new FileResponse($filePath, 404, ['X-Header' => 'Foo'], null, true, true);

        $response
            ->if($condition)
            ->setContent('hello')
            ->else()
            ->setContent(null)
            ->fi();

        $this->assertSame(404, $response->getStatusCode());
        $this->assertSame('Foo', $response->headers->get('X-Header'));
        $this->assertTrue($response->headers->has('ETag'));
        $this->assertTrue($response->headers->has('Last-Modified'));
        $this->assertFalse($response->headers->has('Content-Disposition'));

        unlink($filePath);
    }

    public function testSetContentFlow2(): void
    {
        $this->expectException(\LogicException::class);
        $this->expectExceptionMessage(
            'The content cannot be set on a FileResponse instance.'
        );

        $condition = true;

        $filePath = __DIR__.'/assert/setContentFlow_test2.txt';

        file_put_contents($filePath, 'foo');

        $response = new FileResponse($filePath, 404, ['X-Header' => 'Foo'], null, true, true);

        $response
            ->if($condition)
            ->setContent('hello')
            ->else()
            ->setContent(null)
            ->fi();

        $this->assertSame(404, $response->getStatusCode());
        $this->assertSame('Foo', $response->headers->get('X-Header'));
        $this->assertTrue($response->headers->has('ETag'));
        $this->assertTrue($response->headers->has('Last-Modified'));
        $this->assertFalse($response->headers->has('Content-Disposition'));
    }

    public function testSetContentDispositionFlow(): void
    {
        $condition = false;

        $filePath = __DIR__.'/assert/setContentDisposition_test.txt';

        file_put_contents($filePath, 'foo');

        $response = new FileResponse($filePath, 404, ['X-Header' => 'Foo'], null, true, true);

        $response
            ->if($condition)
            ->setContentDisposition('inline', 'foo.txt')
            ->else()
            ->setContentDisposition('attachment', 'bar.txt')
            ->fi();

        $this->assertSame(404, $response->getStatusCode());
        $this->assertSame('Foo', $response->headers->get('X-Header'));
        $this->assertTrue($response->headers->has('ETag'));
        $this->assertTrue($response->headers->has('Last-Modified'));
        $this->assertTrue($response->headers->has('Content-Disposition'));
        $this->assertSame('attachment; filename="bar.txt"', $response->headers->get('Content-Disposition'));

        unlink($filePath);
    }

    public function testSetContentDispositionFlow2(): void
    {
        $condition = true;

        $filePath = __DIR__.'/assert/setContentDisposition_test2.txt';

        file_put_contents($filePath, 'foo');

        $response = new FileResponse($filePath, 404, ['X-Header' => 'Foo'], null, true, true);

        $response
            ->if($condition)
            ->setContentDisposition('inline', 'foo.txt')
            ->else()
            ->setContentDisposition('attachment', 'bar.txt')
            ->fi();

        $this->assertSame(404, $response->getStatusCode());
        $this->assertSame('Foo', $response->headers->get('X-Header'));
        $this->assertTrue($response->headers->has('ETag'));
        $this->assertTrue($response->headers->has('Last-Modified'));
        $this->assertTrue($response->headers->has('Content-Disposition'));
        $this->assertSame('inline; filename="foo.txt"', $response->headers->get('Content-Disposition'));

        unlink($filePath);
    }
}
