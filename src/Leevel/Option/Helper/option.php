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

namespace Leevel\Option\Helper;

use Leevel\Di\Container;
use Leevel\Option\IOption;

/**
 * 配置服务
 *
 * @return \Leevel\Option\IOption
 */
function option(): IOption
{
    return Container::singletons()->make('option');
}

/**
 * 获取 opiton 值
 *
 * @param string     $key
 * @param mixed      $defaults
 * @param null|mixed $default
 *
 * @return mixed
 */
function option_get($key, $default = null)
{
    return option()->get($key, $default);
}

/**
 * 设置 opiton 值
 *
 * @param mixed $name
 * @param mixed $value
 */
function option_set($name, $value = null): void
{
    option()->set($name, $value);
}

class option
{
}
