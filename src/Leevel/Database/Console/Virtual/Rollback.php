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

namespace Leevel\Database\Console\Virtual;

use Leevel\Console\Command;
use RuntimeException;

/**
 * 虚拟数据库迁移回滚数据库脚本.
 *
 * @codeCoverageIgnore
 */
class Rollback extends Command
{
    /**
     * 命令名字.
     *
     * @var string
     */
    protected string $name = 'migrate:rollback';

    /**
     * 命令行描述.
     *
     * @var string
     */
    protected string $description = 'Just a virtual migrate:rollback.';

    /**
     * 响应命令.
     *
     * @throws \RuntimeException
     */
    public function handle(): void
    {
        $e = 'If you execute command `composer dump-autoload --optimize --no-dev`,'.
            'then this will not be available.'.PHP_EOL.
            'Phinx belongs to development dependence and `composer dump-autoload --optimize` is ok.';

        throw new RuntimeException($e);
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
        return [];
    }
}
