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

namespace Leevel\Router\Console;

use Leevel\Console\Command;
use Leevel\Filesystem\Helper\create_file;
use function Leevel\Filesystem\Helper\create_file;
use Leevel\Kernel\IApp;
use Leevel\Router\RouterProvider;

/**
 * openapi 路由缓存.
 */
class Cache extends Command
{
    /**
     * 命令名字.
    */
    protected string $name = 'router:cache';

    /**
     * 命令行描述.
    */
    protected string $description = 'Annotations as the router';

    /**
     * 响应命令.
     */
    public function handle(IApp $app, RouterProvider $routerProvider): int
    {
        $this->line('Start to cache router.');
        $data = $routerProvider->getRouters();
        $cachePath = $app->routerCachedPath();
        $this->writeCache($cachePath, $data);
        $this->info(sprintf('Router cache successed at %s.', $cachePath));

        return 0;
    }

    /**
     * 写入缓存.
     */
    protected function writeCache(string $cachePath, array $data): void
    {
        $content = '<?php /* '.date('Y-m-d H:i:s').' */ ?>'.
            PHP_EOL.'<?php return '.var_export($data, true).'; ?>';
        create_file($cachePath, $content);
    }
}

// import fn.
class_exists(create_file::class);
