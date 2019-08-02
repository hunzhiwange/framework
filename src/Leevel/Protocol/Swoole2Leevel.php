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

namespace Leevel\Protocol;

use Leevel\Http\IRequest;
use Leevel\Http\Request;
use Swoole\Http\Request as SwooleHttpRequest;

/**
 * Swoole 规范请求转 Leevel.
 *
 * - 剥离自 \Leevel\Protocol\HttpServer
 *
 * @author Xiangmin Liu <635750556@qq.com>
 *
 * @since 2019.08.01
 *
 * @version 1.0
 */
class Swoole2Leevel
{
    /**
     * 从 Swoole 请求对象创建 Leevel 请求对象.
     *
     * @param \Psr\Http\Message\ServerRequestInterface $psrRequest
     *
     * @return \Leevel\Http\IRequest
     */
    public function createRequest(SwooleHttpRequest $swooleRequest): IRequest
    {
        $request = new Request();
        $this->normalizeSwooleHeaderAndServer($swooleRequest);
        $this->convertRequest($swooleRequest, $request);

        return $request;
    }

    /**
     * 整理 Swoole 请求 Header 和 Server.
     *
     * @param \Swoole\Http\Request $swooleRequest
     */
    protected function normalizeSwooleHeaderAndServer(SwooleHttpRequest $swooleRequest): void
    {
        $servers = [];

        if ($swooleRequest->header) {
            $headers = [];

            foreach ($swooleRequest->header as $key => $value) {
                $key = strtoupper(str_replace('-', '_', $key));
                $headers[$key] = $value;
                $servers['HTTP_'.$key] = $value;
            }

            $swooleRequest->header = $headers;
        }

        $this->normalizeSwooleServer($swooleRequest, $servers);
    }

    /**
     * 整理 Swoole 请求 Server.
     *
     * @param \Swoole\Http\Request $swooleRequest
     * @param array                $servers
     */
    protected function normalizeSwooleServer(SwooleHttpRequest $swooleRequest, array $servers): void
    {
        if ($swooleRequest->server) {
            $swooleRequest->server = array_change_key_case(
                $swooleRequest->server,
                CASE_UPPER
            );

            $servers = array_merge($servers, $swooleRequest->server);
            $swooleRequest->server = $servers;
        } else {
            $swooleRequest->server = $servers ?: null;
        }
    }

    /**
     * 转换请求.
     *
     * @param \Swoole\Http\Request  $swooleRequest
     * @param \Leevel\Http\IRequest $request
     */
    protected function convertRequest(SwooleHttpRequest $swooleRequest, IRequest $request): void
    {
        $propMap = [
            'header' => 'headers',
            'server' => 'server',
            'cookie' => 'cookies',
            'get'    => 'query',
            'files'  => 'files',
            'post'   => 'request',
        ];

        foreach ($propMap as $swooleProp => $prop) {
            if ($swooleRequest->{$swooleProp}) {
                $request->{$prop}->replace($swooleRequest->{$swooleProp});
            }
        }
    }
}
