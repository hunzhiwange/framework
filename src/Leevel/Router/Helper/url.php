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

namespace Leevel\Router\Helper;

use Leevel\Di\Container;

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
function url(string $url, array $params = [], string $subdomain = 'www', $suffix = null): string
{
    /** @var \Leevel\Router\Url $service */
    $service = Container::singletons()->make('url');

    return $service->make($url, $params, $subdomain, $suffix);
}

class url
{
}
