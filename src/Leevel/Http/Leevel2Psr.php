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

namespace Leevel\Http;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\UploadedFileInterface;
use function Zend\Diactoros\normalizeServer;
use function Zend\Diactoros\normalizeUploadedFiles;
use Zend\Diactoros\Response as DiactorosResponse;
use Zend\Diactoros\ServerRequest;
use Zend\Diactoros\Stream as DiactorosStream;
use Zend\Diactoros\UploadedFile as DiactorosUploadedFile;

/**
 * Leevel 规范请求转 Psr.
 * This class borrows heavily from the Symfony4 Framework and is part of the symfony package.
 *
 * @author Xiangmin Liu <635750556@qq.com>
 * @author Kévin Dunglas <dunglas@gmail.com>
 *
 * @since 2019.03.11
 *
 * @version 1.0
 *
 * @see Symfony\Bridge\PsrHttpMessage (https://github.com/symfony/psr-http-message-bridge)
 */
class Leevel2Psr
{
    /**
     * 从 Leevel 请求对象创建 Psr 请求对象.
     *
     * @param \Leevel\Http\IRequest $leevelRequest
     *
     * @return \Psr\Http\Message\ServerRequestInterface
     */
    public function createRequest(IRequest $leevelRequest): ServerRequestInterface
    {
        $server = normalizeServer($leevelRequest->server->all());
        $headers = $leevelRequest->headers->all();

        $body = new DiactorosStream($this->normalizeContent($leevelRequest->getContent()));

        $request = new ServerRequest(
            $server,
            normalizeUploadedFiles($this->getFiles($leevelRequest->files->all())),
            $leevelRequest->getSchemeAndHttpHost().$leevelRequest->getRequestUri(),
            $leevelRequest->getMethod(),
            $body,
            $headers
        );

        $request = $request
            ->withCookieParams($leevelRequest->cookies->all())
            ->withQueryParams($leevelRequest->query->all())
            ->withParsedBody($leevelRequest->request->all())
            ->withRequestTarget($leevelRequest->getRequestUri());

        foreach ($leevelRequest->params->all() as $key => $value) {
            $request = $request->withAttribute($key, $value);
        }

        return $request;
    }

    /**
     * 从 Leevel 响应对象创建 Psr 响应对象.
     *
     * @param \Leevel\Http\IResponse $leevelResponse
     *
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function createResponse(Response $leevelResponse): ResponseInterface
    {
        $stream = new DiactorosStream('php://temp', 'wb+');
        $stream->write($leevelResponse->getContent());

        $headers = $leevelResponse->headers->all();

        if (!isset($headers['Set-Cookie']) &&
            !isset($headers['set-sookie']) &&
            $cookies = $leevelResponse->getCookies()) {
            foreach ($cookies as $item) {
                $headers['Set-Cookie'][] = Cookie::format($item);
            }
        }

        $response = new DiactorosResponse(
            $stream,
            $leevelResponse->getStatusCode(),
            $headers
        );

        $protocolVersion = $leevelResponse->getProtocolVersion();

        if ('1.1' !== $protocolVersion) {
            $response = $response->withProtocolVersion($protocolVersion);
        }

        return $response;
    }

    /**
     * 格式化响应内容.
     *
     * @param string $content
     *
     * @return resource
     */
    protected function normalizeContent(string $content)
    {
        $resource = fopen('php://temp', 'r+');
        fwrite($resource, $content);
        rewind($resource);

        return $resource;
    }

    /**
     * 转换上传文件结构.
     *
     * @param array $uploadedFiles
     *
     * @return array
     */
    protected function getFiles(array $uploadedFiles): array
    {
        $files = [];

        foreach ($uploadedFiles as $key => $value) {
            if (null === $value) {
                $files[$key] = new DiactorosUploadedFile(null, 0, UPLOAD_ERR_NO_FILE, null, null);

                continue;
            }

            if ($value instanceof UploadedFile) {
                $files[$key] = $this->createUploadedFile($value);
            } else {
                $files[$key] = $this->getFiles($value);
            }
        }

        return $files;
    }

    /**
     * 创建 PSR-7 上传对象.
     *
     * @param \Leevel\Http\UploadedFile $leevelUploadedFile
     *
     * @return \Psr\Http\Message\UploadedFileInterface
     */
    protected function createUploadedFile(UploadedFile $leevelUploadedFile): UploadedFileInterface
    {
        return new DiactorosUploadedFile(
            $leevelUploadedFile->getRealPath(),
            (int) $leevelUploadedFile->getSize(),
            $leevelUploadedFile->getError(),
            $leevelUploadedFile->getOriginalName(),
            $leevelUploadedFile->getMimeType()
        );
    }
}
