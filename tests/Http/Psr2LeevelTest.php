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

use Leevel\Http\Psr2Leevel;
use Tests\Http\Fixtures\Response;
use Tests\TestCase;
use Zend\Diactoros\ServerRequest;
use Zend\Diactoros\Stream;
use Zend\Diactoros\UploadedFile;

/**
 * Psr2LeevelTest test.
 *
 * - This class borrows heavily from the Symfony4 Framework and is part of the symfony package.
 *
 * @author Xiangmin Liu <635750556@qq.com>
 *
 * @since 2019.04.03
 * @see https://github.com/symfony/psr-http-message-bridge/blob/master/Tests/Factory/HttpFoundationFactoryTest.php
 *
 * @version 1.0
 */
class Psr2LeevelTest extends TestCase
{
    public function testBaseUse(): void
    {
        // see https://github.com/symfony/psr-http-message-bridge/blob/master/Tests/Factory/HttpFoundationFactoryTest.php#L57
        $uploadedFiles = [
            'doc1'   => $this->createUploadedFile('Doc 1', UPLOAD_ERR_OK, 'doc1.txt', 'text/plain'),
            'nested' => [
                'docs' => [
                    $this->createUploadedFile('Doc 2', UPLOAD_ERR_OK, 'doc2.txt', 'text/plain'),
                    $this->createUploadedFile('Doc 3', UPLOAD_ERR_OK, 'doc3.txt', 'text/plain'),
                ],
            ],
        ];

        $serverRequest = new ServerRequest(
            ['HTTP_HOST' => 'queryphp.cn'],
            $uploadedFiles,
            'http://queryphp.cn/foo/bar/hello.html',
            'GET',
            new Stream(fopen(__DIR__.'/assert/stream.txt', 'r')),
            ['foo'     => 'bar', 'hello' => 'world'],
            ['cookie1' => 'world'],
            ['query1'  => 'foo', 'query2' => 'bar'],
            null,
            '1.1'
        );

        $p2l = new Psr2Leevel();
        $leevelRequest = $p2l->createRequest($serverRequest);

        $this->assertEquals('foo', $leevelRequest->query->get('query1'));
        $this->assertEquals('bar', $leevelRequest->query->get('query2'));

        $files = $leevelRequest->files->all();
        $this->assertCount(3, $files);
        $this->assertEquals('doc1.txt', $files['doc1']->getOriginalName());
        $this->assertEquals('doc2.txt', $files['nested\\docs\\0']->getOriginalName());
        $this->assertEquals('doc3.txt', $files['nested\\docs\\1']->getOriginalName());

        $this->assertEquals('queryphp.cn', $leevelRequest->headers->get('host'));
        $this->assertEquals('queryphp.cn', $leevelRequest->server->get('SERVER_NAME'));
        $this->assertEquals('/foo/bar/hello.html', $leevelRequest->server->get('REQUEST_URI'));
        $this->assertEquals('GET', $leevelRequest->server->get('REQUEST_METHOD'));
        $this->assertEquals('hello world', $leevelRequest->getContent());
    }

    public function testCreateResponse(): void
    {
        $response = new Response(
            '1.0',
            [
                'Set-Cookie' => [
                    'theme=light',
                    'test',
                    'ABC=AeD; Domain=dunglas.fr; Path=/kevin; Expires=Wed, 13 Jan 2021 22:23:01 GMT; Secure; HttpOnly; SameSite=Strict',
                ],
            ],
            new Stream(fopen(__DIR__.'/assert/stream.txt', 'r')),
            200
        );

        $p2l = new Psr2Leevel();
        $leevelResponse = $p2l->createResponse($response);
        $this->assertEquals('1.0', $leevelResponse->getProtocolVersion());
        $cookies = $leevelResponse->headers->getCookies();
        $value = <<<'eot'
            {
                "theme": [
                    "theme",
                    "light",
                    0,
                    "\/",
                    null,
                    false,
                    false
                ],
                "test": [
                    "test",
                    null,
                    0,
                    "\/",
                    null,
                    false,
                    false
                ],
                "ABC": [
                    "ABC",
                    "AeD",
                    1610576581,
                    "\/kevin",
                    "dunglas.fr",
                    true,
                    true
                ]
            }
            eot;

        $this->assertSame(
            $value,
            $this->varJson(
                $cookies
            )
        );

        $this->assertEquals('hello world', $leevelResponse->getContent());
        $this->assertEquals(200, $leevelResponse->getStatusCode());
    }

    public function testCreateResponseButCookieIsInvalid(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('The value of the Set-Cookie header is malformed.');

        $response = new Response(
            '1.0',
            [
                'Set-Cookie' => [
                    'theme=light',
                    ' ',
                ],
            ],
            new Stream(fopen(__DIR__.'/assert/stream.txt', 'r')),
            200
        );

        $p2l = new Psr2Leevel();
        $leevelResponse = $p2l->createResponse($response);
    }

    private function createUploadedFile($content, $error, $clientFileName, $clientMediaType)
    {
        $filePath = tempnam(sys_get_temp_dir(), uniqid());
        file_put_contents($filePath, $content);

        return new UploadedFile($filePath, filesize($filePath), $error, $clientFileName, $clientMediaType);
    }
}
