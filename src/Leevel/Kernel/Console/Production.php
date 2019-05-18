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

namespace Leevel\Kernel\Console;

use Leevel\Console\Command;
use Leevel\Console\Option;

/**
 * 生产环境性能一键优化.
 *
 * @author Xiangmin Liu <635750556@qq.com>
 *
 * @since 2018.09.02
 *
 * @version 1.0
 * @codeCoverageIgnore
 */
class Production extends Command
{
    /**
     * 命令名字.
     *
     * @var string
     */
    protected string $name = 'production';

    /**
     * 命令行描述.
     *
     * @var string
     */
    protected string $description = 'Let your app run faster in production.';

    /**
     * 响应命令.
     */
    public function handle(): void
    {
        $this->line('Start to optimize you app.');

        $this->callRouter();

        $this->callOption();

        $this->callI18n();

        $this->callView();

        $this->callAutoload();

        $this->line('');
        $this->info('Optimize successed.');
    }

    /**
     * 执行路由缓存.
     */
    protected function callRouter(): void
    {
        $this->line('');
        $this->call('router:cache');
    }

    /**
     * 执行配置缓存.
     */
    protected function callOption(): void
    {
        $this->line('');
        $this->call('option:cache');
    }

    /**
     * 执行语言缓存.
     */
    protected function callI18n(): void
    {
        $this->line('');
        $this->call('i18n:cache');
    }

    /**
     * 执行视图缓存.
     */
    protected function callView(): void
    {
        $this->line('');
        $this->call('view:cache');
    }

    /**
     * 执行自动载入缓存.
     */
    protected function callAutoload(): void
    {
        $this->line('');
        $this->call('autoload', [
            '--composer' => $this->composer(),
        ]);
    }

    /**
     * 取得 Composer 路径.
     *
     * @return string
     */
    protected function composer(): string
    {
        return $this->option('composer');
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
        return [
            [
                'composer',
                null,
                Option::VALUE_OPTIONAL,
                'Where is composer.',
                'composer',
            ],
        ];
    }
}
