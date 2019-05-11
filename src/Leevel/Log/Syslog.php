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

namespace Leevel\Log;

use Leevel\Event\IDispatch;
use Monolog\Handler\SyslogHandler;
use Psr\Log\LoggerInterface;

/**
 * 系统日志.
 *
 * @author Xiangmin Liu <635750556@qq.com>
 *
 * @since 2017.09.01
 *
 * @version 1.0
 */
class Syslog extends Log implements ILog
{
    /**
     * 配置.
     *
     * @see \Monolog\Handler\AbstractSyslogHandler
     *
     * @var array
     */
    protected $option = [
        'levels'   => [
            ILog::DEBUG,
            ILog::INFO,
            ILog::NOTICE,
            ILog::WARNING,
            ILog::ERROR,
            ILog::CRITICAL,
            ILog::ALERT,
            ILog::EMERGENCY,
        ],
        'buffer'      => true,
        'buffer_size' => 100,
        'channel'     => 'development',
        'facility'    => LOG_USER,
        'level'       => ILog::DEBUG,
    ];

    /**
     * 构造函数.
     *
     * @param array                   $option
     * @param \Leevel\Event\IDispatch $dispatch
     */
    public function __construct(array $option = [], IDispatch $dispatch = null)
    {
        parent::__construct($option, $dispatch);

        $this->createMonolog();

        $this->makeSyslogHandler();
    }

    /**
     * 初始化系统 handler.
     *
     * @return \Psr\Log\LoggerInterface
     */
    protected function makeSyslogHandler(): LoggerInterface
    {
        $handler = new SyslogHandler($this->option['channel'],
            $this->option['facility'],
            $this->normalizeMonologLevel($this->option['level'])
        );

        return $this->monolog->pushHandler($this->normalizeHandler($handler));
    }
}
