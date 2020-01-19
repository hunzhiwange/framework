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

namespace Leevel\Http;

use InvalidArgumentException;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\UploadedFileInterface;
use Psr\Http\Message\UriInterface;

/**
 * Psr 规范请求转 Leevel.
 *
 * - This class borrows heavily from the Symfony4 Framework and is part of the symfony package.
 *
 * @author Kévin Dunglas <dunglas@gmail.com>
 *
 * @see Symfony\Bridge\PsrHttpMessage (https://github.com/symfony/psr-http-message-bridge)
 */
class Psr2Leevel
{
    /**
     * 从 Psr 请求对象创建 Leevel 请求对象.
     *
     * @return \Leevel\Http\Request
     */
    public function createRequest(ServerRequestInterface $psrRequest): Request
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
     * 从 Psr 响应对象创建 Leevel 响应对象.
     *
     * @return \Leevel\Http\Response
     */
    public function createResponse(ResponseInterface $psrResponse): Response
    {
        $response = new Response(
            (string) $psrResponse->getBody(),
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
     * @throws \InvalidArgumentException
     */
    protected function createCookie(string $cookie): array
    {
        $cookieName = $cookieValue = null;
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
                $cookieExpire = strtotime($value) - time();

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

        if (!$cookieName) {
            $e = 'The value of the Set-Cookie header is malformed.';

            throw new InvalidArgumentException($e);
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
