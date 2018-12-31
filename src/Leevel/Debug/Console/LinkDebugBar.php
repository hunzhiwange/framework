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

namespace Leevel\Debug\Console;

use Leevel\Console\Command;
use Leevel\Filesystem\Fso;
use Leevel\Kernel\IProject;

/**
 * debuger 资源目录创建软连接到 www.
 *
 * @author Xiangmin Liu <635750556@qq.com>
 *
 * @since 2018.09.21
 *
 * @version 1.0
 * @codeCoverageIgnore
 */
class LinkDebugBar extends Command
{
    /**
     * 命令名字.
     *
     * @var string
     */
    protected $name = 'link:debugbar';

    /**
     * 命令行描述.
     *
     * @var string
     */
    protected $description = 'Create a symbolic link from `vendor/maximebf/debugbar/src/DebugBar/Resources` to `www/debugbar`.';

    /**
     * 响应命令.
     *
     * @param \Leevel\Kernel\IProject $project
     */
    public function handle(IProject $project)
    {
        if (file_exists($link = $project->path('www/debugbar'))) {
            return $this->error(
                sprintf('The `%s` directory already exists.', $link)
            );
        }

        Fso::link(
            $path = $project->path('vendor/maximebf/debugbar/src/DebugBar/Resources'), $link
        );

        $this->info(sprintf('Linked `%s` directory to `%s` successed.', $path, $link));
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
