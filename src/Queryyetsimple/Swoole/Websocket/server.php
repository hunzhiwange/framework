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
namespace queryyetsimple\swoole\websocket;

use Exception;
use Swoole\{
    Http\Request as SwooleHttpRequest,
    Websocket\Frame as SwooleWebsocketFrame,
    Websocket\Server as SwooleWebsocketServer
};
use queryyetsimple\swoole\http\server as servers;

/**
 * swoole websocket 服务
 *
 * @author Xiangmin Liu <635750556@qq.com>
 * @package $$
 * @since 2017.12.30
 * @see https://wiki.swoole.com/wiki/page/397.html
 * @version 1.0
 */
class server extends servers
{
    
    /**
     * 配置
     * 
     * @var array
     */
    protected $arrOption = [
        // 监听 IP 地址
        // see https://wiki.swoole.com/wiki/page/p-server.html
        // see https://wiki.swoole.com/wiki/page/327.html
        'host' => '0.0.0.0', 
        
        // 监听端口
        // see https://wiki.swoole.com/wiki/page/p-server.html
        // see https://wiki.swoole.com/wiki/page/327.html
        'port' => '9501', 
        
        // swoole 进程名称
        'process_name' => 'queryswoolewebsocket', 
        
        // swoole 进程保存路径
        'pid_path' => '', 
        
        // 设置启动的 worker 进程数
        // see https://wiki.swoole.com/wiki/page/275.html
        'worker_num' => 8, 

        // 守护进程化
        // see https://wiki.swoole.com/wiki/page/278.html
        'daemonize' => 0
    ];
    
    /**
     * 服务回调事件
     * 
     * @var array
     */
    protected $arrServerEvent = [
        'start', 
        'connect', 
        'workerStart', 
        'managerStart', 
        'workerStop',
        'request',
        'shutdown',
        'open',
        'message',
        'task',
        'finish',
        'close'
    ];
    
    /**
     * WebSocket客户端与服务器建立连接并完成握手后
     * 
     * @param \Swoole\Websocket\Server $objServer
     * @param \Swoole\Http\Request $objRequest
     * @see https://wiki.swoole.com/wiki/page/401.html
     * @return void
     */
    public function onOpen(SwooleWebsocketServer $objServer, SwooleHttpRequest $objRequest) {
        $this->line(sprintf('Server: handshake success with fd %s', $objRequest->fd));
    }

    /**
     * 监听服务器收到来自客户端的数据帧
     * 
     * @param \Swoole\Websocket\Server $objServer
     * @param \Swoole\Websocket\Frame $objFrame
     * @see https://wiki.swoole.com/wiki/page/397.html
     * @return void
     */
    public function onMessage(SwooleWebsocketServer $objServer, SwooleWebsocketFrame $objFrame) {
        $this->line(sprintf('Receive from fd %d:%s,opcode:%d,fin:%d', $objFrame->fd, $objFrame->data, $objFrame->opcode, $objFrame->finish));
        $objServer->push($objFrame->fd, "I am from server.");
    }
    
    /**
     * 创建 websocket server
     * 
     * @return void
     */
    protected function createServer()
    {
        $this->objServer = new SwooleWebsocketServer($this->getOption('host'), $this->getOption('port'));
        $this->initServer();
    }
}