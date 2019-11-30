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

namespace Leevel\Kernel\Console;

use Leevel\Console\Command;
use function Leevel\Filesystem\Fso\link;
use Leevel\Filesystem\Fso\link;
use Leevel\Kernel\IApp;

/**
 * apis 文档目录创建软连接到 apis.
 *
 * @author Xiangmin Liu <635750556@qq.com>
 *
 * @since 2018.09.02
 *
 * @version 1.0
 * @codeCoverageIgnore
 */
class LinkApis extends Command
{
    /**
     * 命令名字.
     *
     * @var string
     */
    protected string $name = 'link:apis';

    /**
     * 命令行描述.
     *
     * @var string
     */
    protected string $description = 'Create a symbolic link from `apis` to `www/apis`';

    /**
     * 响应命令.
     *
     * @param \Leevel\Kernel\IApp $app
     */
    public function handle(IApp $app): void
    {
        if (file_exists($link = $app->path('www/apis'))) {
            $this->error(
                sprintf('The `%s` directory already exists.', $link)
            );

            return;
        }

        link($path = $app->path('apis'), $link);
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

// import fn.
class_exists(link::class); // @codeCoverageIgnore
