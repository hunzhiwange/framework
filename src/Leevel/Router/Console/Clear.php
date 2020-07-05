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
use Leevel\Kernel\IApp;

/**
 * 路由缓存清理.
 */
class Clear extends Command
{
    /**
     * 命令名字.
     *
     * @var string
     */
    protected string $name = 'router:clear';

    /**
     * 命令行描述.
     *
     * @var string
     */
    protected string $description = 'Clear cache of router';

    /**
     * 响应命令.
     */
    public function handle(IApp $app): int
    {
        $this->line('Start to clear cache router.');
        $cachePath = $app->routerCachedPath();
        $this->clearCache($cachePath);
        $this->info(sprintf('Router cache files %s clear successed.', $cachePath));

        return 0;
    }

    /**
     * 删除缓存.
     */
    protected function clearCache(string $cachePath): void
    {
        if (!is_file($cachePath)) {
            $this->warn(sprintf('Router cache file %s have been cleaned up.', $cachePath));

            return;
        }

        unlink($cachePath);
    }
}
