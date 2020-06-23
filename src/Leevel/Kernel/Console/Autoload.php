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
use Leevel\Console\Option;

/**
 * 优化 composer 自动加载.
 *
 * @codeCoverageIgnore
 */
class Autoload extends Command
{
    /**
     * 命令名字.
     *
     * @var string
     */
    protected string $name = 'autoload';

    /**
     * 命令行描述.
     *
     * @var string
     */
    protected string $description = 'Optimize base on composer dump-autoload --optimize [--no-dev]';

    /**
     * 响应命令.
     */
    public function handle(): int
    {
        $this->line('Start to cache autoload.');
        $this->line($command = $this->normalizeComposerCommand());
        exec($command);
        $this->info('Autoload cache successed.');

        return 0;
    }

    /**
     * 取得 composer 优化命令.
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
     */
    protected function composer(): string
    {
        return $this->option('composer');
    }

    /**
     * 取得是否为开发模式.
     */
    protected function dev(): bool
    {
        return $this->option('dev');
    }

    /**
     * 命令参数.
     */
    protected function getArguments(): array
    {
        return [];
    }

    /**
     * 命令配置.
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
