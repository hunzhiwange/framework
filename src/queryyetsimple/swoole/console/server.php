<?php
/*
 * This file is part of the ************************ package.
 * ##########################################################
 * #   ____                          ______  _   _ ______   #
 * #  /     \       ___  _ __  _   _ | ___ \| | | || ___ \  #
 * # |   (  ||(_)| / _ \| '__|| | | || |_/ /| |_| || |_/ /  #
 * #  \____/ |___||  __/| |   | |_| ||  __/ |  _  ||  __/   #
 * #       \__   | \___ |_|    \__  || |    | | | || |      #
 * #     Query Yet Simple      __/  |\_|    |_| |_|\_|      #
 * #                          |___ /  Since 2010.10.03      #
 * ##########################################################
 *
 * The PHP Framework For Code Poem As Free As Wind. <Query Yet Simple>
 * (c) 2010-2018 http://queryphp.com All rights reserved.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace queryyetsimple\swoole\console;

use queryyetsimple\{
    console\option,
    console\command,
    console\argument,
    swoole\server\http_server
};

/**
 * swoole 服务管理
 *
 * @author Xiangmin Liu <635750556@qq.com>
 * @package $$
 * @since 2017.12.21
 * @version 1.0
 */
class server extends command
{

    /**
     * 命令名字
     *
     * @var string
     */
    protected $strName = 'swoole:server';

    /**
     * 命令行描述
     *
     * @var string
     */
    protected $strDescription = 'Manage the server by command.';

    /**
     * 响应命令
     *
     * @return void
     */
    public function handle()
    {


        //$this->info('hello swoole');

    

       $this->info($this->getLogo());

       $this->warn($this->getVersion());

       new http_server();
      
       // return;
        // 注册处理的队列
      //  $this->setQueue($this->argument('connect'), $this->option('queue'));

        // 守候进程
       // $this->runWorker($this->argument('connect'), $this->option('queue'));
    }

    /**
     * 任务不可用等待时间
     *
     * @return int
     */
    public function sleep()
    {
        return ( int ) $this->option('sleep');
    }

    /**
     * 返回 QueryPHP Logo
     *
     * @return string
     */
    protected function getLogo()
    {
        return <<<queryphp
##########################################################
#   ____                          ______  _   _ ______   #
#  /     \       ___  _ __  _   _ | ___ \| | | || ___ \  #
# |   (  ||(_)| / _ \| '__|| | | || |_/ /| |_| || |_/ /  #
#  \____/ |___||  __/| |   | |_| ||  __/ |  _  ||  __/   #
#       \__   | \___ |_|    \__  || |    | | | || |      #
#     Query Yet Simple      __/  |\_|    |_| |_|\_|      #
#                          |___ /  Since 2010.10.03      #
##########################################################
queryphp;
    }

    /**
     * 返回 QueryPHP Version
     *
     * @return string
     */
    protected function getVersion()
    {
        return 'The QueryPHP For Swoole Version '.app()->version().PHP_EOL;
    }


    /**
     * 命令参数
     *
     * @return array
     */
    protected function getArguments()
    {
        return [
            [
                'commandargs',
                argument::OPTIONAL,
                'The command of server.',
                'start'
            ]
        ];
    }

    /**
     * 命令配置
     *
     * @return array
     */
    protected function getOptions()
    {
        return [
            // [
            //     'queue',
            //     null,
            //     option::VALUE_OPTIONAL,
            //     'The queue to listen on',
            //     'default'
            // ],
            // [
            //     'memory',
            //     null,
            //     option::VALUE_OPTIONAL,
            //     'The memory limit in megabytes',
            //     128
            // ],
            // [
            //     'sleep',
            //     null,
            //     option::VALUE_OPTIONAL,
            //     'Number of seconds to sleep when no job is available',
            //     5
            // ],
            // [
            //     'tries',
            //     null,
            //     option::VALUE_OPTIONAL,
            //             'Number of times to attempt a job before logging it failed',
            //             0
            //     ]
        ];
    }
}
