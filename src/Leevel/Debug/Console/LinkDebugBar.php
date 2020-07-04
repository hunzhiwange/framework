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

namespace Leevel\Debug\Console;

use Leevel\Console\Command;
use function Leevel\Filesystem\Helper\link;
use Leevel\Filesystem\Helper\link;
use Leevel\Kernel\IApp;

/**
 * debuger 资源目录创建软连接到 www.
 *
 * @codeCoverageIgnore
 */
class LinkDebugBar extends Command
{
    /**
     * 命令名字.
     *
     * @var string
     */
    protected string $name = 'link:debugbar';

    /**
     * 命令行描述.
     *
     * @var string
     */
    protected string $description = 'Create a symbolic link from `vendor/maximebf/debugbar/src/DebugBar/Resources` to `www/debugbar` and `debugbar`,`debugbar` just for Swoole';

    /**
     * 响应命令.
     */
    public function handle(IApp $app): int
    {
        $source = $app->path('vendor/maximebf/debugbar/src/DebugBar/Resources');
        $this->createLink($source, $app->path('www/debugbar'));
        $this->createLink($source, $app->path('debugbar'));

        return 0;
    }

    /**
     * 创建软连接.
     */
    protected function createLink(string $source, string $target): void
    {
        if (file_exists($target)) {
            $this->error(
                sprintf('The `%s` directory already exists.', $target)
            );

            return;
        }

        link($source, $target);
        $this->info(sprintf('Linked `%s` directory to `%s` successed.', $source, $target));
    }
}

// import fn.
class_exists(link::class); // @codeCoverageIgnore
