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

namespace Leevel\Router\Proxy;

use Leevel\Http\IRequest;
use Leevel\Router\IUrl as IBaseUrl;

/**
 * 代理 url 接口.
 *
 * @author Xiangmin Liu <635750556@qq.com>
 *
 * @since 2019.05.24
 *
 * @version 1.0
 *
 * @see \Leevel\Router\IUrl 请保持接口设计的一致性
 */
interface IUrl
{
    /**
     * 生成路由地址
     *
     * @param string           $url
     * @param array            $params
     * @param string           $subdomain
     * @param null|bool|string $suffix
     *
     * @return string
     */
    public static function make(string $url, array $params = [], string $subdomain = 'www', $suffix = null): string;

    /**
     * 返回 HTTP 请求
     *
     * @return \Leevel\Http\IRequest
     */
    public static function getRequest(): IRequest;

    /**
     * 设置配置.
     *
     * @param string $name
     * @param mixed  $value
     *
     * @return \Leevel\Router\IUrl
     */
    public static function setOption(string $name, $value): IBaseUrl;

    /**
     * 获取域名.
     *
     * @return string
     */
    public static function getDomain(): string;
}
