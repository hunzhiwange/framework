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

namespace Leevel\Database\Console;

use Leevel\Database\Console\Virtual\Status as VirtualStatus;
use Phinx\Console\Command\Status as PhinxStatus;

// @codeCoverageIgnoreStart
if (class_exists(PhinxStatus::class)) { 
    class_alias(PhinxStatus::class, __NAMESPACE__.'\\BaseStatus'); 
} else { 
    class_alias(VirtualStatus::class, __NAMESPACE__.'\\BaseStatus'); 
}
// @codeCoverageIgnoreEnd

/**
 * 数据库打印所有迁移脚本和他们的状态
 *
 * @author Xiangmin Liu <635750556@qq.com>
 *
 * @since 2017.05.09
 *
 * @version 1.0
 * @codeCoverageIgnore
 */
class Status extends BaseStatus
{
    /**
     * Configures the current command.
     */
    protected function configure(): void
    {
        parent::configure();

        $this->setName('migrate:status');
    }
}
