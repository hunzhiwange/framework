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
 * 优化 composer 自动加载.
 *
 * @author Xiangmin Liu <635750556@qq.com>
 *
 * @since 2018.09.01
 *
 * @version 1.0
 * @codeCoverageIgnore
 */
class Autoload extends Command
{
    /**
     * 命令名字.
     *
     * @var string
     */
    protected $name = 'autoload';

    /**
     * 命令行描述.
     *
     * @var string
     */
    protected $description = 'Optimize base on composer dump-autoload --optimize [--no-dev].';

    /**
     * 响应命令.
     */
    public function handle(): void
    {
        $this->line('Start to cache autoload.');

        $this->line($command = $this->normalizeComposerCommand());
        exec($command);

        $this->info('Autoload cache successed.');
    }

    /**
     * 取得 composer 优化命令.
     *
     * @return string
     */
    protected function normalizeComposerCommand(): string
    {
        $command = sprintf(
            '%s dump-autoload --optimize',
            escapeshellarg($this->composer())
        );

        if (false === $this->dev()) {
            $command .= ' --no-dev';
        }

        return $command;
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
     * 取得是否为开发模式.
     *
     * @return bool
     */
    protected function dev(): bool
    {
        return $this->option('dev');
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
            [
                'dev',
                '-d',
                Option::VALUE_NONE,
                'Without `--no-dev` option for `composer dump-autoload --optimize`.',
            ],
        ];
    }
}
