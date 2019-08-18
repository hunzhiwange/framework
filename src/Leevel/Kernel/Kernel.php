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
 * (c) 2010-2019 http://queryphp.com All rights reserved.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Leevel\Kernel;

use ErrorException;
use Exception;
use Leevel\Http\IRequest;
use Leevel\Http\IResponse;
use Leevel\Http\Request;
use Leevel\Kernel\Bootstrap\LoadI18n;
use Leevel\Kernel\Bootstrap\LoadOption;
use Leevel\Kernel\Bootstrap\RegisterRuntime;
use Leevel\Kernel\Bootstrap\TraverseProvider;
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
     * 应用.
     *
     * @var \Leevel\Kernel\IApp
     */
    protected IApp $app;

    /**
     * 路由.
     *
     * @var \Leevel\Router\IRouter
     */
    protected IRouter $router;

    /**
     * 应用初始化执行.
     *
     * @var array
     */
    protected array $bootstraps = [
        LoadOption::class,
        LoadI18n::class,
        RegisterRuntime::class,
        TraverseProvider::class,
    ];

    /**
     * 构造函数.
     *
     * @param \Leevel\Kernel\IApp    $app
     * @param \Leevel\Router\IRouter $router
     */
    public function __construct(IApp $app, IRouter $router)
    {
        $this->app = $app;
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

        $this->middlewareTerminate($request, $response);

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
     * 初始化.
     *
     * @codeCoverageIgnore
     */
    public function bootstrap(): void
    {
        $this->app->bootstrap($this->bootstraps);
    }

    /**
     * 返回应用.
     *
     * @return \Leevel\Kernel\IApp
     */
    public function getApp(): IApp
    {
        return $this->app;
    }

    /**
     * 返回运行处理器.
     *
     * @return \Leevel\Kernel\Runtime\IRuntime
     */
    protected function getRuntime(): IRuntime
    {
        return $this->app
            ->container()
            ->make(IRuntime::class);
    }

    /**
     * 注册基础服务
     *
     * @param \Leevel\Http\IRequest $request
     */
    protected function registerBaseService(IRequest $request): void
    {
        $this->app
            ->container()
            ->instance('request', $request);

        $this->app
            ->container()
            ->alias('request', [IRequest::class, Request::class]);
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
     * 上报错误.
     *
     * @param \Exception $e
     */
    protected function reportException(Exception $e): void
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
    protected function middlewareTerminate(IRequest $request, IResponse $response): void
    {
        $this->router->throughMiddleware($request, [$response]);
    }
}
