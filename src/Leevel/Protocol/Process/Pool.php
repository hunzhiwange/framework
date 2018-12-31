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

namespace Leevel\Protocol\Process;

use Leevel\Protocol\IPool;

/**
 * Swoole 对象池清理进程.
 *
 * @author Xiangmin Liu <635750556@qq.com>
 *
 * @since 2018.12.15
 *
 * @version 1.0
 */
class Pool extends Process
{
    /**
     * 进程名字.
     *
     * @var string
     */
    protected $name = 'pool';

    /**
     * 对象池.
     *
     * @var \Leevel\Protocol\IPool
     */
    protected $pool;

    /**
     * 构造函数.
     *
     * @param \Leevel\Protocol\IPool $pool
     */
    public function __construct(IPool $pool)
    {
        $this->pool = $pool;
    }

    /**
     * 响应句柄.
     */
    public function handle(): void
    {
        while (true) {
            sleep(360);

            $this->clear();
        }
    }

    /**
     * 清理.
     */
    public function clear(): void
    {
        foreach ($this->pool->getPools() as $className => &$pool) {
            while ($pool->count()) {
                $pool->shift();
            }
        }

        $this->log('The object pool has cleared.');
    }

    /**
     * 记录日志.
     *
     * @param string $log
     */
    protected function log(string $log): void
    {
        fwrite(STDOUT, $log.PHP_EOL);
    }
}
