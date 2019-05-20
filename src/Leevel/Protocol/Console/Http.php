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

namespace Leevel\Protocol\Console;

use Leevel\Di\Container;
use Leevel\Kernel\Proxy\App;
use Leevel\Protocol\Console\Base\Server as BaseServer;
use Leevel\Protocol\IServer;

/**
 * Http 服务启动.
 *
 * @author Xiangmin Liu <635750556@qq.com>
 *
 * @since 2017.12.21
 *
 * @version 1.0
 * @codeCoverageIgnore
 */
class Http extends BaseServer
{
    /**
     * 命令名字.
     *
     * @var string
     */
    protected $name = 'http:server';

    /**
     * 命令行描述.
     *
     * @var string
     */
    protected $description = 'Start http server';

    /**
     * 命令帮助.
     *
     * @var string
     */
    protected $help = <<<'EOF'
        The <info>%command.name%</info> command to start http server:
        
          <info>php %command.full_name%</info>
        
        You can also by using the <comment>--daemonize</comment> option:
        
          <info>php %command.full_name% --daemonize</info>
        
          <info>php %command.full_name% -d</info>
        EOF;

    /**
     * 创建 server.
     *
     * @return \Leevel\Protocol\IServer
     */
    protected function createServer(): IServer
    {
        return Container::singletons()->make('http.server');
    }

    /**
     * 返回 Version.
     *
     * @return string
     */
    protected function getVersion(): string
    {
        return 'Http Server Version '.App::version().PHP_EOL;
    }
}
