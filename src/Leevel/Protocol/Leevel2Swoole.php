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

use Leevel\Http\IResponse;
use Swoole\Http\Response as SwooleHttpResponse;

/**
 * Leevel 规范请求转 Swoole.
 *
 * - 剥离自 \Leevel\Protocol\HttpServer
 *
 * @author Xiangmin Liu <635750556@qq.com>
 *
 * @since 2019.08.02
 *
 * @version 1.0
 * @codeCoverageIgnore
 */
class Leevel2Swoole
{
    /**
     * 从 Leevel 响应对象创建 Swoole 响应对象.
     *
     * @param \Leevel\Http\IResponse $response
     * @param \Swoole\Http\Response  $swooleResponse
     *
     * @return \Swoole\Http\Response
     */
    public function createResponse(IResponse $response, SwooleHttpResponse $swooleResponse): SwooleHttpResponse
    {
        foreach ($response->getCookies() as $item) {
            call_user_func_array([$swooleResponse, 'cookie'], $item);
        }

        if ($response instanceof RedirectResponse &&
            method_exists($swooleResponse, 'redirect')) {
            $swooleResponse->redirect($response->getTargetUrl());
        }

        foreach ($response->headers->all() as $key => $value) {
            $swooleResponse->header($key, $value);
        }

        $swooleResponse->status($response->getStatusCode());
        $swooleResponse->write($response->getContent() ?: ' ');

        return $swooleResponse;
    }
}
