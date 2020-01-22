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
use JsonSerializable;
use Leevel\Support\IArray;
use Leevel\Support\IJson;
use Symfony\Component\HttpFoundation\JsonResponse as BaseJsonResponse;

/**
 * JSON 响应请求.
 */
class JsonResponse extends BaseJsonResponse
{
    /**
     * 从 JSON 字符串创建响应对象
     *
     * @param int   $status
     * @param array $headers
     *
     * @return static
     */
    public static function fromJsonString(?string $data = null, $status = 200, $headers = []): Response
    {
        return new static($data, $status, $headers, true);
    }

    /**
     * 设置数据作为 JSON.
     *
     * @param mixed $data
     *
     * @throws \InvalidArgumentException
     *
     * @return \Leevel\Http\Response
     */
    public function setData($data = [], ?int $encodingOptions = null): Response
    {
        if (null !== $encodingOptions) {
            $this->encodingOptions = $encodingOptions;
        }

        if ($data instanceof IArray) {
            $this->data = json_encode($data->toArray(), $this->encodingOptions);
        } elseif (is_object($data) && $data instanceof IJson) {
            $this->data = $data->toJson($this->encodingOptions);
        } elseif (is_object($data) && $data instanceof JsonSerializable) {
            $this->data = json_encode($data->jsonSerialize(), $this->encodingOptions);
        } else {
            $this->data = json_encode($data, $this->encodingOptions);
        }

        if (JSON_ERROR_NONE !== json_last_error()) {
            throw new InvalidArgumentException(json_last_error_msg());
        }

        return $this->updateContent();
    }

    /**
     * 取回数据.
     *
     * @return mixed
     */
    public function getData(bool $assoc = true, int $depth = 512)
    {
        return json_decode($this->data, $assoc, $depth);
    }

    /**
     * 获取 JSON 编码参数.
     */
    public function getEncodingOptions(): int
    {
        return $this->encodingOptions;
    }

    /**
     * 设置 JSON 编码参数.
     *
     * @return \Leevel\Http\Response
     */
    public function setEncodingOptions(int $encodingOptions): Response
    {
        $this->encodingOptions = (int) $encodingOptions;

        return $this->setData($this->getData());
    }

    /**
     * 验证是否为正常的 JSON 字符串.
     *
     * @param mixed $data
     */
    protected function isJsonData($data): bool
    {
        if (!is_scalar($data) && !method_exists($data, '__toString')) {
            return false;
        }

        json_decode((string) ($data));

        return JSON_ERROR_NONE === json_last_error();
    }

    /**
     * 更新响应内容.
     *
     * @return \Leevel\Http\Response
     */
    protected function updateContent(): Response
    {
        if (null !== $this->callback) {
            $this->headers->set('Content-Type', 'text/javascript');

            return $this->setContent(sprintf(';%s(%s);', $this->callback, $this->data));
        }

        if (!$this->headers->has('Content-Type') || 'text/javascript' === $this->headers->get('Content-Type')) {
            $this->headers->set('Content-Type', 'application/json');
        }

        return $this->setContent($this->data);
    }
}
