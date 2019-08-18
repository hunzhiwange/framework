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

namespace Leevel\Protocol\Pool;

/**
 * 连接池连接驱动.
 *
 * @author Xiangmin Liu <635750556@qq.com>
 *
 * @since 2019.07.24
 *
 * @version 1.0
 * @codeCoverageIgnore
 */
trait Connection
{
    /**
     * 连接池.
     *
     * @var \Leevel\Protocol\Pool\IPool
     */
    protected IPool $pool;

    /**
     * 是否归还连接池.
     *
     * @var bool
     */
    protected bool $release = false;

    /**
     * 归还连接池.
     */
    public function release(): void
    {
        if ($this->release) {
            $this->release = false;
            $this->pool->returnConnection($this);
        }
    }

    /**
     * 设置是否归还连接池.
     *
     * @param bool $release
     */
    public function setRelease(bool $release): void
    {
        $this->release = $release;
    }

    /**
     * 设置关联连接池.
     *
     * @param \Leevel\Protocol\Pool\IPool $pool
     */
    public function setPool(IPool $pool): void
    {
        $this->pool = $pool;
    }
}
