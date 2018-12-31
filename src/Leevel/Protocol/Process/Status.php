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

namespace Leevel\Protocol\Process;

use Leevel\Protocol\IServer;

/**
 * Swoole 服务状态.
 *
 * @author Xiangmin Liu <635750556@qq.com>
 *
 * @since 2018.12.13
 *
 * @version 1.0
 */
class Status extends Process
{
    /**
     * 进程名字.
     *
     * @var string
     */
    protected $name = 'status';

    /**
     * 响应句柄.
     *
     * @param \Leevel\Protocol\IServer $server
     * @param \Swoole\Process          $worker
     */
    public function handle(IServer $server): void
    {
        while (true) {
            sleep(1);

            $this->status($server);
        }
    }

    /**
     * 查看当前服务状态.
     *
     * @param \Leevel\Protocol\IServer $server
     */
    public function status(IServer $server): void
    {
        // see https://wiki.swoole.com/wiki/page/288.html
        $result = $server->stats();

        $this->log('Item  Value');

        foreach ($result as $key => $val) {
            if ('start_time' === $key) {
                $val = date('Y-m-d H:i:s', $val);
            }

            $this->log(sprintf('%s %s', $key, $val));
        }
    }

    /**
     * 记录日志.
     *
     * @param string $log
     */
    protected function log(string $log): void
    {
        fwrite(STDOUT, $log.PHP_EOL);
    }
}
