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

namespace Leevel\Database\Ddd;

use Leevel\Di\Container;

/**
 * 数据库组件 lazyload.
 */
class Lazyload
{
    /**
     * 延迟载入占位符.
     *
     * - 仅仅用于占位，必要时用于唤醒数据库服务提供者.
     */
    public static function placeholder(): bool
    {
        Container::singletons()->make('database.lazyload');

        return true;
    }
}
