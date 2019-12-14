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

namespace Leevel\Database\Console;

use Leevel\Database\Console\Virtual\Migrate as VirtualMigrate;
use Phinx\Console\Command\Migrate as PhinxMigrate;

// @codeCoverageIgnoreStart
if (class_exists(PhinxMigrate::class)) {
    class_alias(PhinxMigrate::class, __NAMESPACE__.'\\BaseMigrate');
} else {
    class_alias(VirtualMigrate::class, __NAMESPACE__.'\\BaseMigrate');
}
/** @codeCoverageIgnoreEnd */

/**
 * 数据库迁移运行数据库脚本.
 *
 * @codeCoverageIgnore
 */
class Migrate extends BaseMigrate
{
    /**
     * Configures the current command.
     */
    protected function configure(): void
    {
        parent::configure();

        $this->setName('migrate:migrate');
    }
}
