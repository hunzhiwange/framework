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
 * Swoole WebSocket 服务列表.
 *
 * @codeCoverageIgnore
 */
class WebsocketStatus extends BaseStatus
{
    /**
     * 命令名字.
     *
     * @var string
     */
    protected string $name = 'websocket:status';

    /**
     * 命令行描述.
     *
     * @var string
     */
    protected string $description = 'Status of websocket service';

    /**
     * 创建 server.
     */
    protected function createServer(): IServer
    {
        return Container::singletons()->make('websocket.server');
    }

    /**
     * 返回 Version.
     */
    protected function getVersion(): string
    {
        return PHP_EOL.'                  WEBSOCKET STATUS'.PHP_EOL;
    }
}
