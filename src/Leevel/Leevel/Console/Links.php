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

namespace Leevel\Leevel\Console;

use Leevel\Console\Command;

/**
 * 创建系统支持的软连接.
 *
 * @author Xiangmin Liu <635750556@qq.com>
 *
 * @since 2018.09.04
 *
 * @version 1.0
 * @codeCoverageIgnore
 */
class Links extends Command
{
    /**
     * 命令名字.
     *
     * @var string
     */
    protected $name = 'links';

    /**
     * 命令行描述.
     *
     * @var string
     */
    protected $description = 'Create all symbolic links.';

    /**
     * 响应命令.
     */
    public function handle(): void
    {
        $this->line('Start to create symbolic links.');

        $this->callApis();

        $this->callPublic();

        $this->callStorage();

        $this->callDebugBar();

        $this->line('');
        $this->info('Links created successed.');
    }

    /**
     * 执行创建 apis 软连接.
     */
    protected function callApis(): void
    {
        $this->line('');
        $this->call('link:apis');
    }

    /**
     * 执行创建 public 软连接.
     */
    protected function callPublic(): void
    {
        $this->line('');
        $this->call('link:public');
    }

    /**
     * 执行创建 storage 软连接.
     */
    protected function callStorage(): void
    {
        $this->line('');
        $this->call('link:storage');
    }

    /**
     * 执行创建 debugbar 软连接.
     */
    protected function callDebugBar(): void
    {
        $this->line('');
        $this->call('link:debugbar');
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
