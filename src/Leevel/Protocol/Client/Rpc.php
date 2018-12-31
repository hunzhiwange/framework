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

namespace Leevel\Protocol\Client;

use Leevel\Http\IResponse;
use Leevel\Http\JsonResponse;
use Leevel\Http\RedirectResponse;
use Leevel\Http\Response as HttpResponse;
use Leevel\Protocol\Thrift\Service\Request;
use Leevel\Protocol\Thrift\Service\Response;
use Leevel\Protocol\Thrift\Service\ThriftClient;
use Thrift\Protocol\TBinaryProtocol;
use Thrift\Transport\TFramedTransport;
use Thrift\Transport\TSocket;

/**
 * Rpc 客户端.
 *
 * @author Xiangmin Liu <635750556@qq.com>
 *
 * @since 2018.04.03
 *
 * @version 1.0
 */
class Rpc
{
    /**
     * 正确响应为 200
     * 对应 HTTP 状态码
     *
     * @var int
     */
    const OK = 200;

    /**
     * 服务端和客户端的享元数据.
     *
     * @var array
     */
    protected $metas = [];

    /**
     * 构造函数.
     *
     * @param string $id
     */
    public function __construct()
    {
    }

    /**
     * 是否处于协程上下文.
     *
     * @return bool
     */
    public static function coroutineContext(): bool
    {
        return true;
    }

    /**
     * Rpc 调用.
     *
     * @param string $call
     * @param array  $params
     * @param array  $metas
     *
     * @return \Leevel\Http\IResponse
     */
    public function call(string $call, array $params = [], array $metas = []): IResponse
    {
        $transport = $this->makeTransport();
        $protocol = new TBinaryProtocol($transport);
        $transport->open();

        $response = $this->getResponseWithProtocol($protocol, [
            'call'   => $call,
            'params' => $params,
            'metas'  => $metas,
        ]);

        $transport->close();

        $response = $this->normalizeResponse($response);

        return $response;
    }

    /**
     * 设置享元数据.
     *
     * @param array $metas;
     */
    public function setMetas(array $metas): array
    {
        $this->metas = $metas;
    }

    /**
     * 返回享元数据.
     *
     * @return array
     */
    public function getMetas(): array
    {
        return $this->metas;
    }

    /**
     * 添加享元数据.
     *
     * @param array|string $key
     * @param mixed        $value
     */
    public function addMetas($key, $value = null)
    {
        $key = is_array($key) ? $key : [
            $key => $value,
        ];

        foreach ($key as $k => $v) {
            $this->metas[$k] = $v;
        }
    }

    /**
     * 格式化 Thrift Rpc 响应到 QueryPHP 响应.
     *
     * @param \Leevel\Protocol\Thrift\Service\Response $response
     *
     * @return \Leevel\Http\IResponse
     */
    protected function normalizeResponse(Response $response): IResponse
    {
        if ($this->isJson($response->data)) {
            $data = json_decode($response->data, true);

            if (isset($data['target_url'])) {
                return new RedirectResponse($data['target_url'], $response->status);
            }

            return JsonResponse::fromJsonString($response->data, $response->status);
        }

        return new HttpResponse($response->data, $response->status);
    }

    /**
     * 创建传输层
     *
     * @return \Thrift\Transport\TFramedTransport
     */
    protected function makeTransport(): TFramedTransport
    {
        $socket = new TSocket('127.0.0.1', 1355);

        return new TFramedTransport($socket);
    }

    /**
     * 根据协议返回响应.
     *
     * @param \Thrift\Protocol\TBinaryProtocol $protocol
     * @param array                            $data
     *
     * @return \Leevel\Protocol\Thrift\Service\Response
     */
    protected function getResponseWithProtocol(TBinaryProtocol $protocol, array $data): Response
    {
        $client = new ThriftClient($protocol);

        $message = new Request($data);

        return $client->call($message);
    }

    /**
     * 验证是否为正常的 JSON 字符串.
     *
     * @param mixed $data
     *
     * @return bool
     */
    protected function isJson($data)
    {
        if (!is_scalar($data) && !method_exists($data, '__toString')) {
            return false;
        }

        json_decode((string) ($data));

        return JSON_ERROR_NONE === json_last_error();
    }
}
