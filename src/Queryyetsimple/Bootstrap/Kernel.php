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
namespace Leevel\Bootstrap;

use Exception;
use Leevel\Log\ILog;
use Leevel\Http\Request;
use Leevel\Router\Router;
use Leevel\Http\IResponse;
use Leevel\Http\ApiResponse;
use Leevel\Http\JsonResponse;
use Leevel\Http\RedirectResponse;
use Leevel\Support\Debug\Console;
use Leevel\Bootstrap\Bootstrap\{
    LoadI18n,
    LoadOption,
    RegisterRuntime,
    TraverseProvider
};

/**
 * 内核执行
 *
 * @author Xiangmin Liu <635750556@qq.com>
 * @package $$
 * @since 2016.11.18
 * @version 1.0
 */
abstract class Kernel implements IKernel
{

    /**
     * 项目
     *
     * @var \Leevel\Bootstrap\IProject
     */
    protected $project;

    /**
     * 路由
     *
     * @var \Leevel\Router\Router
     */
    protected $router;

    /**
     * 项目初始化执行
     *
     * @var array
     */
    protected $bootstraps = [
        LoadOption::class,
        LoadI18n::class
        RegisterRuntime::class,
        TraverseProvider::class
    ];

    /**
     * 构造函数
     *
     * @param \Leevel\Bootstrap\IProject $project
     * @param \Leevel\Router\Router $router
     * @return void
     */
    public function __construct(IProject $project, Router $router)
    {
        $this->project = $project;
        $this->router = $router;
    }

    /**
     * 响应 HTTP 请求
     *
     * @param \Leevel\Http\Request $request
     * @return \Leevel\Http\IResponse
     */
    public function handle(Request $request)
    {
        $this->registerBaseService($request);

        $response = $this->getResponseWithRequest($request);

        $response = $this->prepareTrace($response);

        return $response;
    }

    /**
     * 执行结束
     *
     * @param \Leevel\Http\Request $request
     * @param \Leevel\Http\IResponse $response
     * @return void
     */
    public function terminate(Request $request, IResponse $response)
    {
        $this->router->throughMiddleware($request, [
            $response
        ]);
    }

    /**
     * 返回项目
     *
     * @return \Leevel\Bootstrap\IProject
     */
    public function getProject()
    {
        return $this->project;
    }

    /**
     * 注册基础服务
     * 
     * @param \Leevel\Http\Request $request
     * @return void
     */
    protected function registerBaseService(Request $request)
    {
        $this->project->instance('request', $request);
    }

    /**
     * 根据请求返回响应
     *
     * @param \Leevel\Http\Request $request
     * @return \Leevel\Http\IResponse
     */
    protected function getResponseWithRequest(Request $request)
    {
        $this->bootstrap();

        return $this->dispatchRouter($request);
    }

    /**
     * 路由调度
     *
     * @param \Leevel\Http\Request $request
     * @return \Leevel\Http\IResponse
     */
    protected function dispatchRouter(Request $request)
    {
        return $this->router->dispatch($request);
    }

   /**
     * 初始化
     *
     * @return void
     */
    protected function bootstrap()
    {
        $this->project->bootstrap($this->bootstraps);
    }

    /**
     * 调试信息
     *
     * @param \Leevel\Http\Response $response
     * @return \Leevel\Http\IResponse
     */
    protected function prepareTrace(IResponse $response)
    {
        if (! $this->project->debug()) {
            return $response;
        }

        $logs = $this->project[ILog::class]->get();

        if ((
                $response instanceof ApiResponse || 
                $response instanceof JsonResponse || 
                $response->isJson()
            ) && 
                is_array(($data = $response->getData()))) {
            $data['_TRACE'] = Console::jsonTrace($logs);

            $response->setData($data);
        } elseif(! ($response instanceof RedirectResponse)) {
            $data = Console::trace($logs);

            $response->appendContent($data);
        }

        return $response;
    }
}
