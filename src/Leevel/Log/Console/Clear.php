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

namespace Leevel\Log\Console;

use Leevel\Console\Command;
use function Leevel\Filesystem\Fso\delete_directory;
use Leevel\Filesystem\Fso\delete_directory;
use Leevel\Kernel\IApp;

/**
 * log 文件缓存清理.
 *
 * @author Xiangmin Liu <635750556@qq.com>
 *
 * @since 2018.09.04
 *
 * @version 1.0
 * @codeCoverageIgnore
 */
class Clear extends Command
{
    /**
     * 命令名字.
     *
     * @var string
     */
    protected $name = 'log:clear';

    /**
     * 命令行描述.
     *
     * @var string
     */
    protected $description = 'Clear cache of log';

    /**
     * 响应命令.
     *
     * @param \Leevel\Kernel\IApp $app
     */
    public function handle(IApp $app): void
    {
        $this->line('Start to clear cache log.');

        delete_directory($cachePath = $app->runtimePath('log'), true);

        $this->info(sprintf('Log files in path %s cache clear successed.', $cachePath));
    }

    /**
     * 命令参数.
     *
     * @return array
     */
    protected function getArguments(): array
    {
        return [];
    }

    /**
     * 命令配置.
     *
     * @return array
     */
    protected function getOptions(): array
    {
        return [];
    }
}

// import fn.
class_exists(delete_directory::class); // @codeCoverageIgnore
