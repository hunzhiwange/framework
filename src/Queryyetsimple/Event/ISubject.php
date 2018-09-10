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

namespace Leevel\Event;

/**
 * ISubject 接口.
 *
 * @author Xiangmin Liu <635750556@qq.com>
 *
 * @since 2017.06.24
 *
 * @version 1.0
 */
interface ISubject
{
    /**
     * 添加一个观察者角色.
     *
     * @param \SplObserver|string $observer
     *
     * @return $this
     */
    public function attachs($observer);
}
