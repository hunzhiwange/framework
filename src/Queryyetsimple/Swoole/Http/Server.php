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
namespace Queryyetsimple\Swoole\http;

use Exception;
use Swoole\{
    Http\Server as SwooleHttpServer,
    Http\Request as SwooleHttpRequest,
    Http\Response as SwooleHttpResponse
};
use Queryyetsimple\{
    Http\Request,
    Http\Response,
    Router\Router,
    Swoole\Server as Servers
};

/**
 * swoole http 服务
 *
 * @author Xiangmin Liu <635750556@qq.com>
 * @package $$
 * @since 2017.12.25
 * @version 1.0
 */
class Server extends Servers
{
    
    /**
     * 路由
     *
     * @var \queryyetsimple\Router\Router
     */
    protected $objRouter;
    
    /**
     * 请求
     *
     * @var \Queryyetsimple\Http\Request
     */
    protected $objRequest;
    
    /**
     * 响应
     *
     * @var \Queryyetsimple\Http\Response
     */
    protected $objResponse;
    
    /**
     * 配置
     * 
     * @var array
     */
    protected $arrOption = [
        // 监听 IP 地址
        // see https://wiki.swoole.com/wiki/page/p-server.html
        // see https://wiki.swoole.com/wiki/page/327.html
        'host' => '127.0.0.1', 
        
        // 监听端口
        // see https://wiki.swoole.com/wiki/page/p-server.html
        // see https://wiki.swoole.com/wiki/page/327.html
        'port' => '9501', 
        
        // swoole 进程名称
        'process_name' => 'queryswoolehttp', 
        
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
        'close'
    ];
    
    /**
     * 构造函数
     * 
     * @param \queryyetsimple\Router\Router $objRouter
     * @param \Queryyetsimple\Http\Request $objRequest
     * @param \Queryyetsimple\Http\Response $objResponse
     * @param array $arrOption
     * @return void
     */
    public function __construct(Router $objRouter, Request $objRequest, Response $objResponse, array $arrOption = [])
    {
        $this->objRouter = $objRouter;
        $this->objRequest = $objRequest;
        $this->objResponse = $objResponse;
        $this->options($arrOption);
    }

    /**
     * 处理 http 请求
     * 
     * @param \Swoole\Http\Request $objSwooleRequest
     * @param \Swoole\Http\Response $objSwooleResponse
     * @return void
     */
    public function onRequest(SwooleHttpRequest $objSwooleRequest, SwooleHttpResponse $objSwooleResponse)
    {
        // 设置请求数据
        if ($objSwooleRequest->server) {
            $this->objRequest->setServers($objSwooleRequest->server);
        }
        
        if ($objSwooleRequest->get) {
            $this->objRequest->setGets($objSwooleRequest->get);
        }
        
        try {
            // 重置应用环境变量
            // 不然系统会再次获取服务端命令行所在应用信息
            putenv('app_name=null');
            putenv('controller_name=null');
            putenv('action_name=null');

            // 完成路由请求
            $this->objRouter->run();

            // 创建 & 注册
            $objApp = app('Queryyetsimple\Bootstrap\Application')->bootstrap($this->objRouter->app());

            ob_start();
            $objApp->run();
            $strHtml = ob_get_contents();
            ob_end_clean();

            $objSwooleResponse->write($strHtml);

            unset($objApp, $strHtml);
        } catch (Exception $oE) {
            $objSwooleResponse->write($oE->getMessage());
        }
        
        $objSwooleResponse->end();
    }

    /**
     * 创建 http server
     * 
     * @return void
     */
    protected function createServer()
    {
        $this->deleteOption('task_worker_num');
        $this->objServer = new SwooleHttpServer($this->getOption('host'), $this->getOption('port'));
        $this->initServer();
    }
}