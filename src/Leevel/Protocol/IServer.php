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

namespace Leevel\Protocol;

use Swoole\Server as SwooleServer;

/**
 * 协议接口.
 *
 * @author Xiangmin Liu <635750556@qq.com>
 *
 * @since 2018.05.10
 *
 * @version 1.0
 */
interface IServer
{
    /**
     * 设置配置.
     *
     * @param mixed $value
     *
     * @return \Leevel\Protocol\IServer
     */
    public function setOption(string $name, $value): self;

    /**
     * 获取配置.
     */
    public function getOption(): array;

    /**
     * 添加自定义进程.
     *
     * @throws \InvalidArgumentException
     */
    public function process(string $process): void;

    /**
     * 运行服务.
     */
    public function startServer(): void;

    /**
     * 返回服务.
     */
    public function getServer(): SwooleServer;
}
