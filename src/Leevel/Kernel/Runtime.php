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

use Exception;
use Leevel\Http\IRequest;
use Leevel\Http\IResponse;
use Leevel\Http\JsonResponse;
use Leevel\Http\Response;
use Leevel\Kernel\Exception\HttpException;
use Leevel\Log\ILog;
use NunoMaduro\Collision\Provider as CollisionProvider;
use Symfony\Component\Console\Output\OutputInterface;
use Whoops\Handler\JsonResponseHandler;
use Whoops\Handler\PrettyPageHandler;
use Whoops\Run;

/**
 * 异常处理.
 *
 * @author Xiangmin Liu <635750556@qq.com>
 *
 * @since 2017.05.04
 *
 * @version 1.0
 */
abstract class Runtime implements IRuntime
{
    /**
     * 应用.
     *
     * @var \Leevel\Kernel\IApp
     */
    protected IApp $app;

    /**
     * 构造函数.
     *
     * @param \Leevel\Kernel\IApp $app
     */
    public function __construct(IApp $app)
    {
        $this->app = $app;
    }

    /**
     * 异常上报.
     *
     * @param \Exception $e
     *
     * @return mixed
     */
    public function report(Exception $e)
    {
        if (method_exists($e, 'report')) {
            return $e->report();
        }

        // @codeCoverageIgnoreStart
        try {
            $log = $this->app->container()->make(ILog::class);
        } catch (Exception $e) {
            throw $e;
        }
        // @codeCoverageIgnoreEnd

        $log->error($e->getMessage(), ['exception' => (string) $e]);
        $log->flush();
    }

    /**
     * 异常渲染.
     *
     * @param \Leevel\Http\IRequest $request
     * @param \Exception            $e
     *
     * @return \Leevel\Http\IResponse
     */
    public function render(IRequest $request, Exception $e): IResponse
    {
        if (method_exists($e, 'render') && $response = $e->render($request, $e)) {
            if (!($response instanceof IResponse)) {
                $response = new Response($response,
                    $this->normalizeStatusCode($e),
                    $this->normalizeHeaders($e)
                );
            }

            return $response;
        }

        $e = $this->prepareException($e);

        if ($request->isAcceptJson()) {
            return $this->makeJsonResponse($e);
        }

        return $this->makeHttpResponse($e);
    }

    /**
     * 命令行渲染.
     *
     * @param \Symfony\Component\Console\Output\OutputInterface $output
     * @param \Exception                                        $e
     * @codeCoverageIgnore
     */
    public function renderForConsole(OutputInterface $output, Exception $e): void
    {
        if (!class_exists(CollisionProvider::class)) {
            throw $e;
        }

        $handler = (new CollisionProvider())
            ->register()
            ->getHandler()
            ->setOutput($output);

        $handler->setInspector(new Inspector($e));
        $handler->handle();
    }

    /**
     * 尝试返回 HTTP 异常响应.
     *
     * @param \Exception $e
     *
     * @return \Leevel\Http\IResponse
     */
    public function rendorWithHttpExceptionView(Exception $e): IResponse
    {
        $filepath = $this->getHttpExceptionView($e);

        if (file_exists($filepath)) {
            $vars = $this->getExceptionVars($e);
            $content = $this->renderWithFile($filepath, $vars);

            return new Response($content,
                $e->getStatusCode(),
                $e->getHeaders()
            );
        }

        return $this->convertExceptionToResponse($e);
    }

    /**
     * 获取 HTTP 状态的异常模板
     *
     * @param Exception $e
     *
     * @return string
     */
    abstract public function getHttpExceptionView(Exception $e): string;

    /**
     * 获取 HTTP 状态的默认异常模板
     *
     * @return string
     */
    abstract public function getDefaultHttpExceptionView(): string;

    /**
     * HTTP 响应异常.
     *
     * @param \Exception $e
     *
     * @return \Leevel\Http\IResponse
     */
    protected function makeHttpResponse(Exception $e): IResponse
    {
        if ($this->app->debug()) {
            return $this->convertExceptionToResponse($e);
        }

        if (!$this->isHttpException($e)) {
            $e = new class(500, $e->getMessage(), $e->getCode()) extends HttpException {
            };
        }

        return $this->rendorWithHttpExceptionView($e);
    }

    /**
     * JSON 响应异常.
     *
     * @param \Exception $e
     *
     * @return \Leevel\Http\IResponse
     */
    protected function makeJsonResponse(Exception $e): IResponse
    {
        $whoops = $this->makeWhoops();
        $whoops->pushHandler($this->makeJsonResponseHandler());

        // JSON 响应结果的物理路径做安全处理
        $json = $whoops->handleException($e);
        $json = json_decode($json, true);
        $json['code'] = $e->getCode();
        $json['error']['file'] = $this->filterPhysicalPath($json['error']['file']);
        $json = json_encode($json);

        return JsonResponse::fromJsonString($json,
            $this->normalizeStatusCode($e),
            $this->normalizeHeaders($e)
        );
    }

    /**
     * 异常创建响应.
     *
     * @param \Exception $e
     *
     * @return \Leevel\Http\IResponse
     */
    protected function convertExceptionToResponse(Exception $e): IResponse
    {
        return new Response(
            $this->renderExceptionContent($e),
            $this->normalizeStatusCode($e),
            $this->normalizeHeaders($e)
        );
    }

    /**
     * 取得异常默认渲染.
     *
     * @param \Exception $e
     *
     * @return string
     */
    protected function renderExceptionContent(Exception $e): string
    {
        if ($this->app->debug()) {
            return $this->renderExceptionWithWhoops($e);
        }

        return $this->renderExceptionWithDefault($e);
    }

    /**
     * 默认异常渲染.
     *
     * @param \Exception $e
     *
     * @return string
     */
    protected function renderExceptionWithDefault(Exception $e): string
    {
        $vars = $this->getExceptionVars($e);

        return $this->renderWithFile($this->getDefaultHttpExceptionView(), $vars);
    }

    /**
     * Whoops 渲染异常.
     *
     * @param \Exception $e
     *
     * @return string
     */
    protected function renderExceptionWithWhoops(Exception $e): string
    {
        $whoops = $this->makeWhoops();
        $prettyPage = new PrettyPageHandler();
        $prettyPage->handleUnconditionally(true);
        $whoops->pushHandler($prettyPage);

        return $whoops->handleException($e);
    }

    /**
     * 获取异常格式化变量.
     *
     * @param \Exception $e
     *
     * @return array
     */
    protected function getExceptionVars(Exception $e): array
    {
        return [
            'e'              => $e,
            'status_code'    => $this->normalizeStatusCode($e),
            'code'           => $e->getCode(),
            'message'        => $e->getMessage(),
            'type'           => get_class($e),
            'file'           => $this->filterPhysicalPath($e->getFile()),
            'line'           => $e->getLine(),
        ];
    }

    /**
     * 格式化 HTTP 状态码
     *
     * @param \Exception $e
     *
     * @return int
     */
    protected function normalizeStatusCode(Exception $e): int
    {
        return $this->isHttpException($e) ? $e->getStatusCode() : 500;
    }

    /**
     * 格式化响应头.
     *
     * @param \Exception $e
     *
     * @return array
     */
    protected function normalizeHeaders(Exception $e): array
    {
        return $this->isHttpException($e) ? $e->getHeaders() : [];
    }

    /**
     * 创建 Whoops.
     *
     * @return \Whoops\Run
     */
    protected function makeWhoops(): Run
    {
        $whoops = new Run();
        $whoops->writeToOutput(false);
        $whoops->allowQuit(false);

        return $whoops;
    }

    /**
     * 创建 JSON 响应句柄.
     *
     * @return \Whoops\Handler\JsonResponseHandler
     */
    protected function makeJsonResponseHandler(): JsonResponseHandler
    {
        return (new JsonResponseHandler())->addTraceToOutput($this->app->debug());
    }

    /**
     * 准备异常.
     *
     * @param \Exception $e
     *
     * @return \Exception
     */
    protected function prepareException(Exception $e): Exception
    {
        return $e;
    }

    /**
     * 是否为 HTTP 异常.
     *
     * @param \Exception $e
     *
     * @return bool
     */
    protected function isHttpException(Exception $e): bool
    {
        return $e instanceof HttpException;
    }

    /**
     * 通过模板渲染异常.
     *
     * @param string $filepath
     * @param array  $vars
     *
     * @throws \Exception
     *
     * @return string
     */
    protected function renderWithFile(string $filepath, array $vars = []): string
    {
        if (!is_file($filepath)) {
            $e = sprintf('Exception file %s is not extis.', $filepath);

            throw new Exception($e);
        }

        extract($vars);

        ob_start();
        require $filepath;
        $content = ob_get_contents();
        ob_end_clean();

        return $content;
    }

    /**
     * 过滤物理路径
     * 基于安全考虑.
     *
     * @param string $path
     *
     * @return string
     */
    protected function filterPhysicalPath(string $path): string
    {
        return str_replace($this->app->path().'/', '', $path);
    }
}
