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
 * (c) 2010-2018 http://queryphp.com All rights reserved.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Leevel\Bootstrap;

use ErrorException;
use Exception;
use Leevel\Bootstrap\Bootstrap\LoadI18n;
use Leevel\Bootstrap\Bootstrap\LoadOption;
use Leevel\Bootstrap\Bootstrap\RegisterRuntime;
use Leevel\Bootstrap\Bootstrap\TraverseProvider;
use Leevel\Http\IRequest;
use Leevel\Http\IResponse;
use Leevel\Kernel\IKernel;
use Leevel\Kernel\IProject;
use Leevel\Kernel\Runtime\IRuntime;
use Leevel\Router\IRouter;
use Throwable;

/**
 * 内核执行.
 *
 * @author Xiangmin Liu <635750556@qq.com>
 *
 * @since 2016.11.18
 *
 * @version 1.0
 */
abstract class Kernel implements IKernel
{
    /**
     * 项目.
     *
     * @var \Leevel\Kernel\IProject
     */
    protected $project;

    /**
     * 路由.
     *
     * @var \Leevel\Router\IRouter
     */
    protected $router;

    /**
     * 项目初始化执行.
     *
     * @var array
     */
    protected $bootstraps = [
        LoadOption::class,
        LoadI18n::class,
        RegisterRuntime::class,
        TraverseProvider::class,
    ];

    /**
     * 构造函数.
     *
     * @param \Leevel\Kernel\IProject $project
     * @param \Leevel\Router\IRouter  $router
     */
    public function __construct(IProject $project, IRouter $router)
    {
        $this->project = $project;
        $this->router = $router;
    }

    /**
     * 响应 HTTP 请求
     *
     * @param \Leevel\Http\IRequest $request
     *
     * @return \Leevel\Http\IResponse
     */
    public function handle(IRequest $request): IResponse
    {
        try {
            $this->registerBaseService($request);

            $this->bootstrap();

            $response = $this->getResponseWithRequest($request);

            $this->middlewareTerminate($request, $response);
        } catch (Exception $e) {
            $this->reportException($e);

            $response = $this->renderException($request, $e);
        } catch (Throwable $e) {
            $e = new ErrorException(
                $e->getMessage(),
                $e->getCode(),
                E_ERROR,
                $e->getFile(),
                $e->getLine(),
                $e->getPrevious()
            );

            $this->reportException($e);

            $response = $this->renderException($request, $e);
        }

        return $response;
    }

    /**
     * 执行结束
     *
     * @param \Leevel\Http\IRequest  $request
     * @param \Leevel\Http\IResponse $response
     * @codeCoverageIgnore
     */
    public function terminate(IRequest $request, IResponse $response): void
    {
    }

    /**
     * 返回项目.
     *
     * @return \Leevel\Kernel\IProject
     */
    public function getProject(): IProject
    {
        return $this->project;
    }

    /**
     * 返回运行处理器.
     *
     * @return \Leevel\Bootstrap\Runtime\IRuntime
     */
    protected function getRuntime(): IRuntime
    {
        return $this->project->make(IRuntime::class);
    }

    /**
     * 注册基础服务
     *
     * @param \Leevel\Http\IRequest $request
     */
    protected function registerBaseService(IRequest $request)
    {
        $this->project->instance('request', $request);
    }

    /**
     * 根据请求返回响应.
     *
     * @param \Leevel\Http\IRequest $request
     *
     * @return \Leevel\Http\IResponse
     */
    protected function getResponseWithRequest(IRequest $request): IResponse
    {
        return $this->dispatchRouter($request);
    }

    /**
     * 路由调度.
     *
     * @param \Leevel\Http\IRequest $request
     *
     * @return \Leevel\Http\IResponse
     */
    protected function dispatchRouter(IRequest $request): IResponse
    {
        return $this->router->dispatch($request);
    }

    /**
     * 初始化.
     *
     * @codeCoverageIgnore
     */
    protected function bootstrap(): void
    {
        $this->project->bootstrap($this->bootstraps);
    }

    /**
     * 上报错误.
     *
     * @param \Exception $e
     */
    protected function reportException(Exception $e)
    {
        $this->getRuntime()->report($e);
    }

    /**
     * 渲染异常.
     *
     * @param \Leevel\Http\IRequest $request
     * @param \Exception            $e
     *
     * @return \Leevel\Http\IResponse
     */
    protected function renderException(IRequest $request, Exception $e): IResponse
    {
        return $this->getRuntime()->render($request, $e);
    }

    /**
     * 中间件结束响应.
     *
     * @param \Leevel\Http\IRequest  $request
     * @param \Leevel\Http\IResponse $response
     */
    protected function middlewareTerminate(IRequest $request, IResponse $response)
    {
        $this->router->throughMiddleware($request, [
            $response,
        ]);
    }
}
