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

namespace Leevel\Protocol;

use Leevel\Pool;

/**
 * 对象池归还.
 *
 * @author Xiangmin Liu <635750556@qq.com>
 *
 * @since 2018.12.17
 *
 * @version 1.0
 */
trait TPool
{
    /**
     * 析构函数.
     */
    public function __destruct()
    {
        if (!extension_loaded('swoole')) {
            return;
        }

        if (method_exists($this, 'destruct')) {
            $this->destruct();
        }

        Pool::back($this);
    }
}
