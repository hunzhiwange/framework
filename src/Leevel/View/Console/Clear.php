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

namespace Leevel\View\Console;

use Leevel\Console\Command;
use function Leevel\Filesystem\Helper\delete_directory;
use Leevel\Filesystem\Helper\delete_directory;
use Leevel\Kernel\IApp;

/**
 * 视图缓存清理.
 */
class Clear extends Command
{
    /**
     * 命令名字.
     *
     * @var string
     */
    protected string $name = 'view:clear';

    /**
     * 命令行描述.
     *
     * @var string
     */
    protected string $description = 'Clear cache of view';

    /**
     * 响应命令.
     */
    public function handle(IApp $app): int
    {
        $this->line('Start to clear cache view.');
        delete_directory($cachePath = $app->runtimePath('theme'));
        $message = sprintf('View cache files in path %s clear successed.', $cachePath);
        $this->info($message);

        return 0;
    }
}

// import fn.
class_exists(delete_directory::class);
