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

namespace Leevel\Http;

use ArrayObject;
use InvalidArgumentException;
use JsonSerializable;
use Leevel\Support\IArray;
use Leevel\Support\IJson;

/**
 * JSON 响应请求
 * This class borrows heavily from the Symfony4 Framework and is part of the symfony package.
 *
 * @author Xiangmin Liu <635750556@qq.com>
 *
 * @since 2018.02.27
 *
 * @version 1.0
 *
 * @see Symfony\Component\HttpFoundation (https://github.com/symfony/symfony)
 */
class JsonResponse extends Response
{
    /**
     * 默认 JSON 格式化参数.
     *
     * @var int
     */
    const DEFAULT_ENCODING_OPTIONS = 256;
    /**
     * 响应内容.
     *
     * @var sting
     */
    protected $data;

    /**
     * JSON 格式化参数.
     *
     * @var int
     */
    protected $encodingOptions = self::DEFAULT_ENCODING_OPTIONS;

    /**
     * JSONP 回调.
     *
     * @var \callable
     */
    protected $callback;

    /**
     * 构造函数.
     *
     * @param string $data
     * @param int    $status
     * @param array  $headers
     * @param bool   $json
     */
    public function __construct($data = null, int $status = 200, array $headers = [], bool $json = false)
    {
        parent::__construct('', $status, $headers);

        if (null === $data) {
            $data = new ArrayObject();
        }

        $json ? $this->setJson($data) : $this->setData($data);

        $this->isJson = true;
    }

    /**
     * 创建一个 JSON 响应.
     *
     * @param string $data
     * @param int    $status
     * @param array  $headers
     *
     * @return static
     */
    public static function create($data = null, int $status = 200, array $headers = [])
    {
        return new static($data, $status, $headers);
    }

    /**
     * 从 JSON 字符串创建响应对象
     *
     * @param string $data
     * @param int    $status
     * @param array  $headers
     *
     * @return static
     */
    public static function fromJsonString($data = null, $status = 200, $headers = [])
    {
        return new static($data, $status, $headers, true);
    }

    /**
     * 设置 JSONP 回调.
     *
     * @param null|string $callback
     *
     * @return $this
     */
    public function setCallback($callback = null)
    {
        if ($this->checkTControl()) {
            return $this;
        }

        $this->callback = $callback;

        return $this->updateContent();
    }

    /**
     * 设置原生 JSON 数据.
     *
     * @param string $json
     *
     * @return $this
     */
    public function setJson($json)
    {
        if ($this->checkTControl()) {
            return $this;
        }

        if (!$this->isJsonData($json)) {
            throw new InvalidArgumentException('The method setJson need a json data.');
        }

        $this->data = $json;

        return $this->updateContent();
    }

    /**
     * 设置数据作为 JSON.
     *
     * @param mixed $data
     * @param int   $encodingOptions
     *
     * @return $this
     */
    public function setData($data = [], ?int $encodingOptions = null)
    {
        if ($this->checkTControl()) {
            return $this;
        }

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
     * @param bool $assoc
     * @param int  $depth
     *
     * @return mixed
     */
    public function getData(bool $assoc = true, int $depth = 512)
    {
        return json_decode($this->data, $assoc, $depth);
    }

    /**
     * 获取编码参数.
     *
     * @return int
     */
    public function getEncodingOptions()
    {
        return $this->encodingOptions;
    }

    /**
     * 设置编码参数.
     *
     * @param int $encodingOptions
     *
     * @return $this
     */
    public function setEncodingOptions(int $encodingOptions)
    {
        if ($this->checkTControl()) {
            return $this;
        }

        $this->encodingOptions = (int) $encodingOptions;

        return $this->setData($this->getData());
    }

    /**
     * 验证是否为正常的 JSON 字符串.
     *
     * @param mixed $data
     *
     * @return bool
     */
    protected function isJsonData($data)
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
     * @return $this
     */
    protected function updateContent()
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
