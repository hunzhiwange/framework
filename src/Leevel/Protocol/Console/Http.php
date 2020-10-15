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
use Leevel\Protocol\Console\Base\Server as BaseServer;
use Leevel\Protocol\IServer;

/**
 * HTTP 服务启动.
 */
class Http extends BaseServer
{
    /**
     * 命令名字.
     *
     * @var string
     */
    protected string $name = 'http:server';

    /**
     * 命令行描述.
     *
     * @var string
     */
    protected string $description = 'Start http server';

    /**
     * 命令帮助.
     *
     * @var string
     */
    protected string $help = <<<'EOF'
        The <info>%command.name%</info> command to start http server:
        
          <info>php %command.full_name%</info>
        
        You can also by using the <comment>--daemonize</comment> option:
        
          <info>php %command.full_name% --daemonize</info>
        
          <info>php %command.full_name% -d</info>
        EOF;

    /**
     * 创建 server.
     */
    protected function createServer(): IServer
    {
        return Container::singletons()->make('http.server');
    }

    /**
     * 返回 Version.
     */
    protected function getVersion(): string
    {
        return PHP_EOL.'                     HTTP SERVER'.PHP_EOL;
    }
}
