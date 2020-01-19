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

namespace Leevel\Router;

use Leevel\Http\Request;

/**
 * IUrl 生成.
 */
interface IUrl
{
    /**
     * 生成路由地址.
     *
     * @param null|bool|string $suffix
     */
    public function make(string $url, array $params = [], string $subdomain = 'www', $suffix = null): string;

    /**
     * 返回 HTTP 请求.
     */
    public function getRequest(): Request;

    /**
     * 设置配置.
     *
     * @param mixed $value
     *
     * @return \Leevel\Router\IUrl
     */
    public function setOption(string $name, $value): self;

    /**
     * 获取域名.
     */
    public function getDomain(): string;
}
