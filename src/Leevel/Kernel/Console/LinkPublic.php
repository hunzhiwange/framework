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
use function Leevel\Filesystem\Helper\link;
use Leevel\Filesystem\Helper\link;
use Leevel\Kernel\IApp;

/**
 * public 资源目录创建软连接到 www.
 */
class LinkPublic extends Command
{
    /**
     * 命令名字.
     *
     * @var string
     */
    protected string $name = 'link:public';

    /**
     * 命令行描述.
     *
     * @var string
     */
    protected string $description = 'Create a symbolic link from `public` to `www/public`';

    /**
     * 响应命令.
     */
    public function handle(IApp $app): int
    {
        if (file_exists($link = $app->path('www/public'))) {
            $this->error(sprintf('The `%s` directory already exists.', $link));

            return -1;
        }

        link($path = $app->publicPath(), $link);
        $this->info(sprintf('Linked `%s` directory to `%s` successed.', $path, $link));

        return 0;
    }
}

// import fn.
class_exists(link::class);
