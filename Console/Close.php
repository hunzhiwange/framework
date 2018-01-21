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
namespace Queryyetsimple\Swoole\Console;

use Exception;
use Queryyetsimple\{
    Console\Option,
    Console\Command,
    Console\Argument
};

/**
 * swoole 服务关闭
 *
 * @author Xiangmin Liu <635750556@qq.com>
 * @package $$
 * @since 2017.12.26
 * @version 1.0
 */
class Close extends Command
{

    /**
     * 命令名字
     *
     * @var string
     */
    protected $strName = 'swoole:close';

    /**
     * 命令行描述
     *
     * @var string
     */
    protected $strDescription = 'Close swoole service process.';

    /**
     * 响应命令
     *
     * @return void
     */
    public function handle()
    {
        $this->warn($this->getVersion());
        
        $objServer = app('swoole.' . $this->argument('type').'.server');
        $objServer->setCommand($this);
        $objServer->options($this->parseOption());
        $objServer->closeServer();
    }

    /**
     * 分析参数
     *
     * @return array
     */
    protected function parseOption() :array {
        $arrOption = [];

        foreach(['host', 'port', 'pid_path'] as $sKey) {
            if(! is_null($this->option($sKey))) {
                $arrOption[$sKey] = $this->option($sKey);
            }
        }

        return $arrOption;
    }

    /**
     * 返回 QueryPHP Version
     *
     * @return string
     */
    protected function getVersion()
    {
        return 'The Close of Swoole ' . ucfirst($this->argument('type')) . ' Server Version ' . app()->version() . PHP_EOL;
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
                'type',
                Argument::OPTIONAL,
                'The type of server,support default,http,websocket.',
                app('option')['swoole\default']
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
            [
                'host',
                null,
                Option::VALUE_OPTIONAL,
                'The host to listen on'
            ],
            [
                'port',
                null,
                Option::VALUE_OPTIONAL,
                'The port to listen on'
            ],
            [
                'pid_path',
                null,
                Option::VALUE_OPTIONAL,
                'The save path of process'
            ]
        ];
    }
}
