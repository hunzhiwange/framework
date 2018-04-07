<?php
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
namespace Leevel\Queue\Console;

use Leevel\Console\Command;

/**
 * 重启任务
 *
 * @author Xiangmin Liu <635750556@qq.com>
 * @package $$
 * @since 2017.06.07
 * @version 1.0
 */
class Restart extends Command
{

    /**
     * 命令名字
     *
     * @var string
     */
    protected $strName = 'queue:restart';

    /**
     * 命令行描述
     *
     * @var string
     */
    protected $strDescription = 'Restart queue work after done it current job.';

    /**
     * 响应命令
     *
     * @return void
     */
    public function handle()
    {
        cache()->set('queryphp.queue.restart', time(), [
            'expire' => 0
        ]);
        $this->info('Send queue restart signal.');
    }
}
