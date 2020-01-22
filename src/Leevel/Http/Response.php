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

use ArrayObject;
use InvalidArgumentException;
use JsonSerializable;
use Leevel\Support\IArray;
use Leevel\Support\IJson;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpFoundation\Response as BaseResponse;

/**
 * HTTP 响应.
 */
class Response extends BaseResponse
{
    /**
     * 是否为 JSON.
     *
     * @var bool
     */
    protected bool $isJson = false;

    /**
     * {@inheritdoc}
     */
    public function setContent($content): self
    {
        if ($this->contentShouldJson($content)) {
            $this->setHeader('Content-Type', 'application/json');
            $this->isJson = true;
            $this->content = (string) $this->contentToJson($content);

            return $this;
        }

        return parent::sendContent($content);
    }

    /**
     * 附加内容.
     *
     * @return \Leevel\Http\Response
     */
    public function appendContent(?string $content = null): self
    {
        $this->content = $this->getContent().$content;

        return $this;
    }

    /**
     * 设置响应头.
     *
     * @param string|string[] $values
     */
    public function setHeader(string $key, $values, bool $replace = true): void
    {
        $this->headers->set($key, $values, $replace);
    }

    /**
     * 批量设置响应头.
     */
    public function withHeaders(array $headers, bool $replace = true): void
    {
        foreach ($headers as $key => $value) {
            $this->headers->set($key, $value, $replace);
        }
    }

    /**
     * 设置 COOKIE.
     */
    public function setCookie(Cookie $cookie): void
    {
        $this->headers->setCookie($cookie);
    }

    /**
     * 批量设置 COOKIE.
     */
    public function withCookies(array $cookies): void
    {
        foreach ($cookies as $cookie) {
            $this->headers->setCookie($cookie);
        }
    }

    /**
     * 获取 COOKIE.
     */
    public function getCookies(): array
    {
        return $this->headers->getCookies();
    }

    /**
     * 取回 JSON 数据.
     *
     * @return mixed
     */
    public function getData(bool $assoc = true, int $depth = 512)
    {
        if ($this->isJson) {
            return json_decode($this->content, $assoc, $depth);
        }

        return $this->content;
    }

    /**
     * 设置 JSON 数据.
     *
     * @param mixed $data
     *
     * @throws \InvalidArgumentException
     */
    public function setData($data = [], ?int $encodingOptions = null): void
    {
        if (null === $encodingOptions) {
            $encodingOptions = JSON_UNESCAPED_UNICODE;
        }

        if ($data instanceof IArray) {
            $data = json_encode($data->toArray(), $encodingOptions);
        } elseif (is_object($data) && $data instanceof IJson) {
            $data = $data->toJson($encodingOptions);
        } elseif (is_object($data) && $data instanceof JsonSerializable) {
            $data = json_encode($data->jsonSerialize(), $encodingOptions);
        } else {
            $data = json_encode($data, $encodingOptions);
        }

        if (JSON_ERROR_NONE !== json_last_error()) {
            throw new InvalidArgumentException(json_last_error_msg());
        }

        $this->content = (string) $data;
    }

    /**
     * 设置响应内容类型.
     *
     * @return \Leevel\Http\Response
     */
    public function setContentType(string $contentType, ?string $charset = null): self
    {
        if (null === $charset) {
            $charset = $this->getCharset();
        }

        if (null === $charset) {
            $this->setHeader('Content-Type', $contentType);
        } else {
            $this->setHeader('Content-Type', $contentType.'; charset='.$charset);
        }

        return $this;
    }

    /**
     * 设置响应内容长度.
     *
     * @return \Leevel\Http\Response
     */
    public function setContentLength(int $contentLength): self
    {
        $this->setHeader('Content-Length', (string) $contentLength);

        return $this;
    }

    /**
     * 响应是否为 JSON.
     */
    public function isJson(): bool
    {
        return $this->isJson;
    }

    /**
     * 内容转换为 JSON.
     *
     * @param mixed $content
     */
    protected function contentToJson($content): string
    {
        if ($content instanceof IJson) {
            return $content->toJson();
        }

        if ($content instanceof IArray) {
            $content = $content->toArray();
        }

        return json_encode($content, JSON_UNESCAPED_UNICODE);
    }

    /**
     * 可以转换为 JSON.
     *
     * @param mixed $content
     */
    protected function contentShouldJson($content): bool
    {
        return $content instanceof IJson ||
               $content instanceof IArray ||
               $content instanceof ArrayObject ||
               $content instanceof JsonSerializable ||
               is_array($content);
    }
}
