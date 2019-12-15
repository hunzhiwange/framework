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

use Leevel\Database\Console\Virtual\SeedCreate as VirtualSeedCreate;
use Phinx\Console\Command\SeedCreate as PhinxSeedCreate;

// @codeCoverageIgnoreStart
if (class_exists(PhinxSeedCreate::class)) {
    class_alias(PhinxSeedCreate::class, __NAMESPACE__.'\\BaseSeedCreate');
} else {
    class_alias(VirtualSeedCreate::class, __NAMESPACE__.'\\BaseSeedCreate');
}
// @codeCoverageIgnoreEnd

/**
 * 数据库测试数据.
 *
 * @codeCoverageIgnore
 */
class SeedCreate extends BaseSeedCreate
{
    /**
     * Configures the current command.
     */
    protected function configure(): void
    {
        parent::configure();

        $this->setName('migrate:seedcreate');
    }
}
