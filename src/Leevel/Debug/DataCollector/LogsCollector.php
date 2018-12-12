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
 * (c) 2010-2018 http://queryphp.com All rights reserved.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Leevel\Debug\DataCollector;

use DebugBar\DataCollector\MessagesCollector;
use Leevel\Log\ILog;

/**
 * 日志收集器.
 *
 * @author Xiangmin Liu <635750556@qq.com>
 *
 * @since 2018.09.20
 *
 * @version 1.0
 */
class LogsCollector extends MessagesCollector
{
    /**
     * 日志仓储.
     *
     * @var \Leevel\Log\ILog
     */
    protected $log;

    /**
     * 构造函数.
     *
     * @param \Leevel\Log\ILog $log
     */
    public function __construct(ILog $log)
    {
        $this->log = $log;

        parent::__construct('logs');
    }

    /**
     * {@inheritdoc}
     */
    public function collect()
    {
        foreach ($this->log->all() as $log) {
            foreach ($log as $v) {
                $this->log(...$v);
            }
        }

        return parent::collect();
    }
}
