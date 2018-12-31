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

namespace Leevel\Option\Console;

use Leevel\Console\Command;
use Leevel\Console\Option;
use Leevel\Kernel\IProject;

/**
 * 配置缓存清理.
 *
 * @author Xiangmin Liu <635750556@qq.com>
 *
 * @since 2018.05.06
 *
 * @version 1.0
 */
class Clear extends Command
{
    /**
     * 命令名字.
     *
     * @var string
     */
    protected $name = 'option:clear';

    /**
     * 命令行描述.
     *
     * @var string
     */
    protected $description = 'Clear cache of option.';

    /**
     * 响应命令.
     *
     * @param \Leevel\Kernel\IProject $project
     */
    public function handle(IProject $project)
    {
        $this->line('Start to clear cache option.');

        $cachePath = $project->optionCachedPath();

        $this->clearCache($cachePath);

        $this->info(sprintf('Option cache file %s cache clear successed.', $cachePath));
    }

    /**
     * 删除缓存.
     *
     * @param string $cachePath
     */
    protected function clearCache(string $cachePath)
    {
        if (!is_file($cachePath)) {
            $this->warn(sprintf('Option cache file %s have been cleaned up.', $cachePath));

            return;
        }

        unlink($cachePath);
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
