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

use Leevel\Di\Container;
use Leevel\Http\IRequest;
use Leevel\Router\IUrl as IBaseUrl;
use Leevel\Router\Url as BaseUrl;

/**
 * 代理 url.
 *
 * @author Xiangmin Liu <635750556@qq.com>
 *
 * @since 2018.02.08
 *
 * @version 1.0
 * @codeCoverageIgnore
 */
class Url implements IUrl
{
    /**
     * call.
     *
     * @param string $method
     * @param array  $args
     *
     * @return mixed
     */
    public static function __callStatic(string $method, array $args)
    {
        return self::proxy()->{$method}(...$args);
    }

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
    public static function make(string $url, array $params = [], string $subdomain = 'www', $suffix = null): string
    {
        return self::proxy()->make($url, $params, $subdomain, $suffix);
    }

    /**
     * 返回 HTTP 请求
     *
     * @return \Leevel\Http\IRequest
     */
    public static function getRequest(): IRequest
    {
        return self::proxy()->getRequest();
    }

    /**
     * 设置配置.
     *
     * @param string $name
     * @param mixed  $value
     *
     * @return \Leevel\Router\IUrl
     */
    public static function setOption(string $name, $value): IBaseUrl
    {
        return self::proxy()->setOption($name, $value);
    }

    /**
     * 获取域名.
     *
     * @return string
     */
    public static function getDomain(): string
    {
        return self::proxy()->getDomain();
    }

    /**
     * 代理服务
     *
     * @return \Leevel\Router\Url
     */
    public static function proxy(): BaseUrl
    {
        return Container::singletons()->make('url');
    }
}
