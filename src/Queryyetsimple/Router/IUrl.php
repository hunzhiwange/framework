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

namespace Leevel\Router;

use Leevel\Http\IRequest;

/**
 * IUrl 生成.
 *
 * @author Xiangmin Liu <635750556@qq.com>
 *
 * @since 2017.01.10
 *
 * @version 1.0
 */
interface IUrl
{
    /**
     * 生成路由地址
     *
     * @param string $url
     * @param array  $params
     * @param string $subdomain
     * @param mixed  $suffix
     *
     * @return string
     */
    public function make(string $url, array $params = [], string $subdomain = 'www', $suffix = null): string;

    /**
     * 返回 HTTP 请求
     *
     * @return \Leevel\Http\IRequest
     */
    public function getRequest(): IRequest;

    /**
     * 设置配置.
     *
     * @param string $name
     * @param mixed  $value
     */
    public function setOption(string $name, $value): void;
}
