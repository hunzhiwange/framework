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
 * (c) 2010-2019 http://queryphp.com All rights reserved.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tests\Http;

use Leevel\Http\Leevel2Psr;
use Leevel\Http\Request;
use Leevel\Http\Response;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Tests\TestCase;
use Zend\Diactoros\UploadedFile;

/**
 * Leevel2PsrTest test.
 *
 * @author Xiangmin Liu <635750556@qq.com>
 *
 * @since 2019.04.03
 *
 * @version 1.0
 */
class Leevel2PsrTest extends TestCase
{
    protected function setUp(): void
    {
        $dir = sys_get_temp_dir().'/form_test';

        if (!is_dir($dir)) {
            mkdir($dir, 0777, true);
        }
    }

    protected function tearDown(): void
    {
        foreach (glob(sys_get_temp_dir().'/form_test/*') as $file) {
            unlink($file);
        }

        rmdir(sys_get_temp_dir().'/form_test');
    }

    public function testCreateRequest(): void
    {
        $tmpFile = $this->createTempFile();
        $tmpFile2 = $this->createTempFile();

        $files = [
            'file' => [
                'name'     => [basename($tmpFile), basename($tmpFile2)],
                'type'     => ['text/plain', 'text/plain'],
                'tmp_name' => [$tmpFile, $tmpFile2],
                'error'    => [0, 0],
                'size'     => [null, null],
            ],
        ];

        $request = new Request(['hello' => 'world'], [], ['foo' => 'bar'], [], $files, ['REQUEST_URI' => 'http://queryphp.com/hello.html']);
        $l2p = new Leevel2Psr();
        $psrRequest = $l2p->createRequest($request);

        $this->assertInstanceOf(ServerRequestInterface::class, $psrRequest);
        $this->assertSame(['hello' => 'world'], $psrRequest->getQueryParams());
        $this->assertSame(['foo' => 'bar'], $psrRequest->getAttributes());
        $this->assertSame(['REQUEST_URI' => 'http://queryphp.com/hello.html'], $psrRequest->getServerParams());

        $uploadFiles = $psrRequest->getUploadedFiles();
        $this->assertIsArray($uploadFiles);
        $this->assertCount(2, $uploadFiles);
        $this->assertInstanceOf(UploadedFile::class, $uploadFiles['file\\0']);
        $this->assertInstanceOf(UploadedFile::class, $uploadFiles['file\\1']);
    }

    public function testCreateResponse(): void
    {
        $response = new Response('hello world', 200);
        $response->setCookie('foo', 'bar');
        $l2p = new Leevel2Psr();
        $psrResponse = $l2p->createResponse($response);

        $this->assertInstanceOf(ResponseInterface::class, $psrResponse);
        $this->assertSame(200, $psrResponse->getStatusCode());
        $this->assertSame('OK', $psrResponse->getReasonPhrase());
    }

    protected function createTempFile()
    {
        $tempFile = sys_get_temp_dir().'/form_test/'.md5(time().rand()).'.tmp';
        file_put_contents($tempFile, '1');

        return $tempFile;
    }
}
