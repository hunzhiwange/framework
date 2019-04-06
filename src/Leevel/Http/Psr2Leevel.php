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

use DateTime;
use InvalidArgumentException;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\UploadedFileInterface;
use Psr\Http\Message\UriInterface;

/**
 * Psr 规范请求转 Leevel
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
class Psr2Leevel implements IPsr2Leevel
{
    /**
     * 从 Psr 请求对象创建 Leevel 请求对象
     *
     * @param \Psr\Http\Message\ServerRequestInterface $psrRequest
     *
     * @return \Leevel\Http\IRequest
     */
    public function createRequest(ServerRequestInterface $psrRequest): IRequest
    {
        $server = [];
        $uri = $psrRequest->getUri();

        if ($uri instanceof UriInterface) {
            $server['SERVER_NAME'] = $uri->getHost();
            $server['SERVER_PORT'] = $uri->getPort();
            $server['REQUEST_URI'] = $uri->getPath();
            $server['QUERY_STRING'] = $uri->getQuery();
        }

        $server['REQUEST_METHOD'] = $psrRequest->getMethod();

        $server = array_replace($server, $psrRequest->getServerParams());

        $parsedBody = $psrRequest->getParsedBody();
        $parsedBody = is_array($parsedBody) ? $parsedBody : [];

        $request = new Request(
            $psrRequest->getQueryParams(),
            $parsedBody,
            $psrRequest->getAttributes(),
            $psrRequest->getCookieParams(),
            $this->getFiles($psrRequest->getUploadedFiles()),
            $server,
            $psrRequest->getBody()->__toString()
        );

        return $request;
    }

    /**
     * 从 Psr 响应对象创建 Leevel 响应对象
     *
     * @param \Psr\Http\Message\ResponseInterface $psrResponse
     *
     * @return \Leevel\Http\IResponse
     */
    public function createResponse(ResponseInterface $psrResponse): IResponse
    {
        $response = new Response(
            $psrResponse->getBody()->__toString(),
            $psrResponse->getStatusCode(),
            $psrResponse->getHeaders()
        );

        $response->setProtocolVersion($psrResponse->getProtocolVersion());

        foreach ($psrResponse->getHeader('Set-Cookie') as $cookie) {
            $response->setCookie(...$this->createCookie($cookie));
        }

        return $response;
    }

    /**
     * 转换上传文件结构.
     *
     * @param array  $uploadedFiles
     * @param string $parent
     *
     * @return array
     */
    protected function getFiles(array $uploadedFiles, string $parent = ''): array
    {
        $files = [];

        foreach ($uploadedFiles as $key => $value) {
            $key = ($parent ? $parent.'\\' : '').$key;

            if ($value instanceof UploadedFileInterface) {
                $files[$key] = $this->createUploadedFile($value);
            } else {
                $files = array_merge($files, $this->getFiles($value, $key));
            }
        }

        return $files;
    }

    /**
     * 创建 Leevel 上传对象.
     *
     * @param \Psr\Http\Message\UploadedFileInterface $psrUploadedFile
     *
     * @return \Leevel\Http\UploadedFile
     */
    protected function createUploadedFile(UploadedFileInterface $psrUploadedFile): UploadedFile
    {
        $temporaryPath = '';
        $clientFileName = '';

        if (UPLOAD_ERR_NO_FILE !== $psrUploadedFile->getError()) {
            $temporaryPath = $this->getTemporaryPath();
            $psrUploadedFile->moveTo($temporaryPath);

            $clientFileName = $psrUploadedFile->getClientFilename();
        }

        return new UploadedFile(
            $temporaryPath,
            $clientFileName ?: '',
            $psrUploadedFile->getClientMediaType(),
            $psrUploadedFile->getError()
        );
    }

    /**
     * 获取上传文件临时目录.
     *
     * @return string
     */
    protected function getTemporaryPath(): string
    {
        return tempnam(sys_get_temp_dir(), uniqid('leevel', true));
    }

    /**
     * 创建 COOKIE 结构.
     *
     * Some snippets have been taken from the Guzzle app: https://github.com/guzzle/guzzle/blob/5.3/src/Cookie/SetCookie.php#L34
     *
     * @param string $cookie
     *
     * @return array
     */
    protected function createCookie(string $cookie): array
    {
        foreach (explode(';', $cookie) as $part) {
            $part = trim($part);

            $data = explode('=', $part, 2);
            $name = $data[0];
            $value = isset($data[1]) ? trim($data[1], " \n\r\t\0\x0B\"") : null;

            if (!isset($cookieName)) {
                $cookieName = $name;
                $cookieValue = $value;

                continue;
            }

            if ('expires' === strtolower($name) && null !== $value) {
                $cookieExpire = new DateTime($value);

                continue;
            }

            if ('path' === strtolower($name) && null !== $value) {
                $cookiePath = $value;

                continue;
            }

            if ('domain' === strtolower($name) && null !== $value) {
                $cookieDomain = $value;

                continue;
            }

            if ('secure' === strtolower($name)) {
                $cookieSecure = true;

                continue;
            }

            if ('httponly' === strtolower($name)) {
                $cookieHttpOnly = true;

                continue;
            }
        }

        if (!isset($cookieName)) {
            throw new InvalidArgumentException('The value of the Set-Cookie header is malformed.');
        }

        return [
            $cookieName,
            $cookieValue,
            [
                'expire'   => $cookieExpire ?? 0,
                'path'     => $cookiePath ?? '/',
                'domain'   => $cookieDomain ?? null,
                'secure'   => isset($cookieSecure),
                'httponly' => isset($cookieHttpOnly),
            ],
        ];
    }
}
