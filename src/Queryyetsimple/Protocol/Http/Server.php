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
namespace Leevel\Protocol\Http;

use Throwable;
use Swoole\{
    Http\Server as SwooleHttpServer,
    Http\Request as SwooleHttpRequest,
    Http\Response as SwooleHttpResponse
};
use Leevel\{
    Http\Request,
    Router\Router,
    Router\ResponseFactory,
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
     * @var \leevel\Router\Router
     */
    protected $objRouter;
    
    /**
     * 请求
     *
     * @var \Leevel\Http\Request
     */
    protected $request;
    
    /**
     * 响应
     *
     * @var \Leevel\Http\Response
     */
    protected $response;
    
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
        'port' => '9501', 
        
        // swoole 进程名称
        'process_name' => 'queryphp.swoole.http', 
        
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
     * @param \leevel\Router\Router $objRouter
     * @param \Leevel\Http\Request $request
     * @param \Leevel\Router\ResponseFactory $response
     * @param array $option
     * @return void
     */
    public function __construct(Router $objRouter, Request $request, ResponseFactory $response, array $option = [])
    {
        $this->objRouter = $objRouter;
        $this->request = $request;
        $this->response = $response;
        $this->options($option);
    }

    /**
     * 处理 http 请求
     * 浏览器连接服务器后, 页面上的每个请求均会执行一次
     * 每次打开链接页面默认都是接收两个请求, 一个是正常的数据请求, 一个 favicon.ico 的请求
     * 
     * @param \Swoole\Http\Request $swooleRequest
     * @param \Swoole\Http\Response $swooleResponse
     * @return void
     */
    public function onRequest(SwooleHttpRequest $swooleRequest, SwooleHttpResponse $swooleResponse)
    {
        // 请求过滤
        if ($swooleRequest->server['path_info'] == '/favicon.ico' || 
            $swooleRequest->server['request_uri'] == '/favicon.ico') {
            return $swooleResponse->end();
        }

        // 设置请求数据
        $this->request->reset();

        $datas = [
            'header' => 'headers',
            'server' => 'server',
            'cookie' => 'cookies',
            'get' => 'query', 
            'files' => 'files',
            'post' => 'request'
        ];

        $servers = [];

        if ($swooleRequest->header) {
            $tmp = $tmpHeader = [];

            foreach ($swooleRequest->header as $key => $value) {
                $key = strtoupper(str_replace('-', '_', $key));
                $tmpHeader[$key] = $value;

                $key = 'HTTP_' . $key;
                $tmp[$key] = $value;
            }

            $servers = $tmp;
            $swooleRequest->header = $tmpHeader;
        }

        if ($swooleRequest->server) {
            $swooleRequest->server = array_change_key_case($swooleRequest->server, CASE_UPPER);
            
            $servers = array_merge($servers, $swooleRequest->server);
            $swooleRequest->server = $servers;
        } else {
            $swooleRequest->server = $servers ?: null;
        }
        
        foreach ($datas as $key => $item) {
            if ($swooleRequest->{$key}) {
                $this->request->{$item}->replace($swooleRequest->{$key});
            }
        }

        try {
            // 重置应用环境变量
            // 不然系统会再次获取服务端命令行所在应用信息
            putenv('app_name=null');
            putenv('controller_name=null');
            putenv('action_name=null');

            // 完成路由请求
            app()->appRouter();

            //ob_start();
            app()->appRun();

            //$content = ob_get_contents();
            //ob_end_clean();

            //$swooleResponse->write($content ?: ' ');
        } catch (Throwable $e) {
            $swooleResponse->write($e->getMessage());
        }
        
        $swooleResponse->end();
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