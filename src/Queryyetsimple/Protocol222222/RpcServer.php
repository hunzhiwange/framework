<?php declare(strict_types=1);
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
namespace Leevel\Protocol;

use Exception;
use Thrift\Server\TServerSocket;
use Swoole\Server as SwooleServer;
use Thrift\Factory\TBinaryProtocolFactory;
use Leevel\Swoole\Server as Servers;
use Leevel\Protocol\Thrift\{
    Base\ThriftServer,
    Service\ThriftHandler,
    Service\ThriftProcessor,
    Base\TFramedTransportFactory
};

/**
 * swoole rpc 服务
 *
 * @author Xiangmin Liu <635750556@qq.com>
 * @package $$
 * @since 2018.04.04
 * @link https://wiki.swoole.com/wiki/page/287.html 
 * @version 1.0
 */
class RpcServer extends Servers
{
    
    /**
     * 配置
     * 
     * @var array
     */
    protected $option = [
        // 监听 IP 地址
        // see https://wiki.swoole.com/wiki/page/p-server.html
        // see https://wiki.swoole.com/wiki/page/327.html
        'host' => '127.0.0.1', 
        
        // 监听端口
        // see https://wiki.swoole.com/wiki/page/p-server.html
        // see https://wiki.swoole.com/wiki/page/327.html
        'port' => '1355', 
        
        // swoole 进程名称
        'process_name' => 'queryphp.swoole.rpc', 
        
        // swoole 进程保存路径
        'pid_path' => '', 
        
        // 设置启动的 worker 进程数
        // see https://wiki.swoole.com/wiki/page/275.html
        'worker_num' => 8, 

        // 守护进程化
        // see https://wiki.swoole.com/wiki/page/278.html
        'daemonize' => 0,

        // 数据包分发策略
        // 1：收到会轮询分配给每一个 worker 进程
        // 3：抢占模式，系统会根据 worker 进程的闲置状态，只会投递给闲置的 worker 进程
        // https://wiki.swoole.com/wiki/page/277.html
        'dispatch_mode' => 1,
        
        // 打开包长检测
        // 包体长度检测提供了固定包头和包体这种协议格式的检测。
        // 启用后可以保证 worker 进程 onReceive 每一次都收到完整的包
        // https://wiki.swoole.com/wiki/page/287.html         
        'open_length_check' => true,
                                  
        // 最大请求包长度，8M
        // https://wiki.swoole.com/wiki/page/301.html
        'package_max_length' => 8192000,

        // 长度的类型,参见 PHP 的 pack 函数
        // http://php.net/manual/zh/function.pack.php
        // https://wiki.swoole.com/wiki/page/463.html
        'package_length_type' => 'N', 
        
        // 第 N 个字节是包长度的值
        // 如果未 0，表示整个包，包含包体和包头
        // https://wiki.swoole.com/wiki/page/287.html
        'package_length_offset' => 0,
                                    
        // 从第几个字节计算长度
        // https://wiki.swoole.com/wiki/page/287.html
        'package_body_offset' => 4
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
        'receive',
        'shutdown',
        'task',
        'finish',
        'close'
    ];

    protected $thriftServer;

    /**
     * 构造函数
     * 
     * @param array $option
     * @return void
     */
    public function __construct(array $option = [])
    {
        parent::__construct($option);

        $this->thriftServer = $this->makeThriftServer();
    }
    
    /**
     * 创建 websocket server
     * 
     * @return void
     */
    //protected function createServer()
    //{
        //$this->objServer = new SwooleWebsocketServer($this->getOption('host'), $this->getOption('port'));
        //$this->initServer();
    //}

    /**                                                                                                 }
     * 监听数据发送事件
     * 
     * @param \Swoole\Server $objServer
     * @param int $intFd
     * @param int $intReactorId
     * @param string $strData
     * @link https://wiki.swoole.com/wiki/page/50.html
     * @return void
     */
    public function onReceive(SwooleServer $objServer, int $intFd, int $intReactorId, string $strData)
    {
        parent::onReceive($objServer, $intFd, $intReactorId, $strData);

        $this->thriftServer->receive($objServer, $intFd, $intReactorId, $strData);
    }


    protected function makeThriftServer()
    {
        $service = new ThriftHandler();
        $processor = new ThriftProcessor($service);
        $socketTranport = new TServerSocket($this->getOption('host'), $this->getOption('port'));
        $outFactory = $inFactory = new TFramedTransportFactory();
        $outProtocol = $inProtocol = new TBinaryProtocolFactory();

        $server = new ThriftServer($processor, $socketTranport, $inFactory, $outFactory, $inProtocol, $outProtocol);
        
        return $server;
    }
}
