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
namespace Leevel\Swoole\Console;

use Leevel\{
    Console\Option,
    Console\Command,
    Console\Argument
};

/**
 * swoole 服务启动
 *
 * @author Xiangmin Liu <635750556@qq.com>
 * @package $$
 * @since 2017.12.21
 * @version 1.0
 */
class Server extends Command
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
    protected $strDescription = 'Start swoole server.';

    /**
     * 响应命令
     *
     * @return void
     */
    public function handle()
    {
        $this->info($this->getLogo());
        $this->warn($this->getVersion());

        $objServer = app('swoole.' . $this->argument('type') . '.server');
        $objServer->setCommand($this);
        $objServer->options($this->parseOption());
        $objServer->startServer();
    }

    /**
     * 分析参数
     *
     * @return array
     */
    protected function parseOption() :array {
        $arrOption = [];

        foreach(['host', 'port', 'pid_path', 'worker_num', 'daemonize'] as $sKey) {
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
        return 'Swoole ' . ucfirst($this->argument('type')) . ' Server Version ' . app()->version() . PHP_EOL;
    }

    /**
     * 返回 QueryPHP Logo
     *
     * @return string
     */
    protected function getLogo()
    {
        return <<<queryphp
_____________                           _______________
 ______/     \__  _____  ____  ______  / /_  _________
  ____/ __   / / / / _ \/ __`\/ / __ \/ __ \/ __ \___
   __/ / /  / /_/ /  __/ /  \  / /_/ / / / / /_/ /__
     \_\ \_/\____/\___/_/   / / .___/_/ /_/ .___/
        \_\                /_/_/         /_/
queryphp;
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
            ],
            [
                'worker_num',
                null,
                Option::VALUE_OPTIONAL,
                'Set the number of worker processes'
            ],
            [
                'daemonize',
                null,
                Option::VALUE_OPTIONAL,
                'Daemon process'
            ]
        ];
    }
}
