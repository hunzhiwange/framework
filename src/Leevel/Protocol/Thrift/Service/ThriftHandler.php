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

namespace Leevel\Protocol\Thrift\Service;

use InvalidArgumentException;
use Leevel\Http\IResponse;
use Leevel\Http\RedirectResponse;
use Leevel\Http\Request as HttpRequest;
use Leevel\Kernel\IKernel;
use Leevel\Leevel;

/**
 * thrift 默认服务调用响应.
 *
 * @author Xiangmin Liu <635750556@qq.com>
 *
 * @since 2018.04.01
 *
 * @version 1.0
 */
class ThriftHandler implements ThriftIf
{
    /**
     * 变量名储存键.
     *
     * @var string
     */
    const VARS = '_vars';

    /**
     * 定义一个响应包结构.
     *
     * @param \Leevel\Protocol\Thrift\Service\Request $request
     *
     * @return \Leevel\Protocol\Thrift\Service\Response
     */
    public function call(Request $request): Response
    {
        $httpRequest = $this->normalizeRequest($request);

        $response = Leevel::make(IKernel::class)->handle($httpRequest);

        return $this->normalizeResponse($response);
    }

    /**
     * 格式化 QueryPHP 响应到 Thrift RPC 响应.
     *
     * @param \Leevel\Http\Request $response
     *
     * @return \Leevel\Protocol\Thrift\Service\Response
     */
    protected function normalizeResponse(IResponse $response): Response
    {
        if ($response instanceof RedirectResponse) {
            $content = json_encode(['target_url' => $response->getTargetUrl()]);
        } else {
            $content = $response->getContent();
        }

        return new Response([
            'status' => $response->getStatusCode(),
            'data'   => $content,
        ]);
    }

    /**
     * 格式化 Thrift RPC 请求到 QueryPHP 请求
     *
     * @param \Leevel\Protocol\Thrift\Service\Request $request
     *
     * @return \Leevel\Http\Request
     */
    protected function normalizeRequest(Request $request): HttpRequest
    {
        if (!$request->call) {
            throw new InvalidArgumentException('Rpc call is not set.');
        }

        $params = $request->metas ?: [];
        $params[self::VARS] = $request->params ?: [];

        $httpRequest = new HttpRequest([], [], $params);

        if (false !== strpos($request->call, '@')) {
            list($call, $method) = explode('@', $request->call);
            $httpRequest->setMethod($method);
        } else {
            $call = $request->call;
        }

        $httpRequest->setPathInfo($call);

        return $httpRequest;
    }
}
