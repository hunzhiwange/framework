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

use Leevel\Di\Container;
use Leevel\Kernel\Proxy\App as ProxyApp;

/**
 * 代理 app 别名.
 */
class App extends ProxyApp
{
}

/**
 * 代理 app 别名.
 */
class Leevel extends ProxyApp
{
}

if (!function_exists('__')) {
    /**
     * 获取语言.
     *
     * @param string $text
     * @param array  ...$data
     *
     * @return string
     * @codeCoverageIgnore
     */
    function __(string $text, ...$data): string
    {
        /** @var \Leevel\I18n\I18n $service */
        $service = Container::singletons()->make('i18n');

        return $service->gettext($text, ...$data);
    }
}
