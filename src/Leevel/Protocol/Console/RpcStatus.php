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

namespace Leevel\Protocol\Console;

use Leevel\Di\Container;
use Leevel\Protocol\Console\Base\Status as BaseStatus;
use Leevel\Protocol\IServer;

/**
 * Swoole RPC 服务列表.
 *
 * @author Xiangmin Liu <635750556@qq.com>
 *
 * @since 2017.12.26
 *
 * @version 1.0
 * @codeCoverageIgnore
 */
class RpcStatus extends BaseStatus
{
    /**
     * 命令名字.
     *
     * @var string
     */
    protected string $name = 'rpc:status';

    /**
     * 命令行描述.
     *
     * @var string
     */
    protected string $description = 'Status of rpc service';

    /**
     * 创建 server.
     */
    protected function createServer(): IServer
    {
        return Container::singletons()->make('rpc.server');
    }

    /**
     * 返回 Version.
     */
    protected function getVersion(): string
    {
        return PHP_EOL.'                      RPC STATUS'.PHP_EOL;
    }
}
