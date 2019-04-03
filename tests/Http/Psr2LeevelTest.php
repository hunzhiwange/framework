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
use Tests\TestCase;
use Zend\Diactoros\ServerRequest;
use Zend\Diactoros\Stream;
use Zend\Diactoros\UploadedFile;

/**
 * Psr2LeevelTest test.
 *
 * @author Xiangmin Liu <635750556@qq.com>
 *
 * @since 2019.04.03
 *
 * @version 1.0
 */
class Psr2LeevelTest extends TestCase
{
    public function testBaseUse()
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
        $this->assertSame(3, count($files));
        $this->assertEquals('doc1.txt', $files['doc1']->getOriginalName());
        $this->assertEquals('doc2.txt', $files['nested\\docs\\0']->getOriginalName());
        $this->assertEquals('doc3.txt', $files['nested\\docs\\1']->getOriginalName());

        $this->assertEquals('queryphp.cn', $leevelRequest->headers->get('host'));
        $this->assertEquals('queryphp.cn', $leevelRequest->server->get('SERVER_NAME'));
        $this->assertEquals('/foo/bar/hello.html', $leevelRequest->server->get('REQUEST_URI'));
        $this->assertEquals('GET', $leevelRequest->server->get('REQUEST_METHOD'));
        $this->assertEquals('hello world', $leevelRequest->getContent());
    }

    private function createUploadedFile($content, $error, $clientFileName, $clientMediaType)
    {
        $filePath = tempnam(sys_get_temp_dir(), uniqid());
        file_put_contents($filePath, $content);

        return new UploadedFile($filePath, filesize($filePath), $error, $clientFileName, $clientMediaType);
    }
}
