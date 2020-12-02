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
use Leevel\Protocol\Console\Base\Reload as BaseReload;
use Leevel\Protocol\IServer;

/**
 * HTTP 服务重启.
 */
class HttpReload extends BaseReload
{
    /**
     * 命令名字.
    */
    protected string $name = 'http:reload';

    /**
     * 命令行描述.
    */
    protected string $description = 'Reload http service';

    /**
     * {@inheritdoc}
     */
    protected function createServer(): IServer
    {
        return Container::singletons()->make('http.server');
    }

    /**
     * {@inheritdoc}
     */
    protected function getVersion(): string
    {
        return PHP_EOL.'                     HTTP RELOAD'.PHP_EOL;
    }
}
