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
 * (c) 2010-2020 http://queryphp.com All rights reserved.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Leevel\Kernel;

use Exception;
use Leevel\Database\Ddd\EntityNotFoundException;
use Leevel\Http\JsonResponse;
use Leevel\Http\Request;
use Leevel\Kernel\Exception\HttpException;
use Leevel\Kernel\Exception\NotFoundHttpException;
use Leevel\Log\ILog;
use Leevel\Router\RouterNotFoundException;
use Leevel\Support\Arr\convert_json;
use function Leevel\Support\Arr\convert_json;
use Leevel\Support\Arr\should_json;
use function Leevel\Support\Arr\should_json;
use NunoMaduro\Collision\Provider as CollisionProvider;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\HttpFoundation\Response;
use Throwable;
use Whoops\Handler\JsonResponseHandler;
use Whoops\Handler\PrettyPageHandler;
use Whoops\Run;

/**
 * 异常运行时.
 */
abstract class ExceptionRuntime implements IExceptionRuntime
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
     * @return mixed
     */
    public function report(Throwable $e)
    {
        if (!$this->reportable($e)) {
            return;
        }

        if (method_exists($e, 'report')) {
            return $e->report();
        }

        $this->reportToLog($e);
    }

    /**
     * 异常是否需要上报.
     */
    public function reportable(Throwable $e): bool
    {
        if (method_exists($e, 'reportable') && false === $e->reportable()) {
            return false;
        }

        return true;
    }

    /**
     * 异常渲染.
     */
    public function render(Request $request, Throwable $e): Response
    {
        if (method_exists($e, 'render') && $response = $e->render($request, $e)) {
            if (!($response instanceof Response)) {
                if (should_json($response)) {
                    $response = JsonResponse::fromJsonString(
                        convert_json($response, JSON_UNESCAPED_UNICODE),
                        $this->normalizeStatusCode($e),
                        $this->normalizeHeaders($e),
                    );
                } else {
                    $response = new Response(
                        $response,
                        $this->normalizeStatusCode($e),
                        $this->normalizeHeaders($e),
                    );
                }
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
     */
    public function renderForConsole(OutputInterface $output, Throwable $e): void
    {
        $handler = (new CollisionProvider())
            ->register()
            ->getHandler()
            ->setOutput($output);
        $handler->setInspector(new Inspector($e));
        $handler->handle();
    }

    /**
     * 获取 HTTP 状态的异常模板.
     */
    abstract public function getHttpExceptionView(HttpException $e): string;

    /**
     * 获取 HTTP 状态的默认异常模板.
     */
    abstract public function getDefaultHttpExceptionView(): string;

    /**
     * 记录异常到日志.
     */
    protected function reportToLog(Throwable $e): void
    {
        $log = $this->app->container()->make(ILog::class);
        $log->error($e->getMessage(), ['exception' => (string) $e]);
        $log->flush();
    }

    /**
     * HTTP 异常响应渲染.
     */
    protected function rendorWithHttpExceptionView(HttpException $e): Response
    {
        if (file_exists($filepath = $this->getHttpExceptionView($e))) {
            $content = $this->renderWithFile($filepath, $this->getExceptionVars($e));

            return new Response(
                $content,
                $e->getStatusCode(),
                $e->getHeaders()
            );
        }

        return $this->convertExceptionToResponse($e);
    }

    /**
     * HTTP 响应异常.
     */
    protected function makeHttpResponse(Throwable $e): Response
    {
        if ($this->app->isDebug()) {
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
     */
    protected function makeJsonResponse(Throwable $e): Response
    {
        $whoops = $this->makeWhoops();
        $whoops->pushHandler($this->makeJsonResponseHandler());

        $json = $whoops->handleException($e);
        $json = json_decode($json, true);
        $json['code'] = $e->getCode();
        $json['error']['file'] = $this->filterPhysicalPath($json['error']['file']);
        $json = json_encode($json);

        return JsonResponse::fromJsonString(
            $json,
            $this->normalizeStatusCode($e),
            $this->normalizeHeaders($e)
        );
    }

    /**
     * 异常创建响应.
     */
    protected function convertExceptionToResponse(Throwable $e): Response
    {
        return new Response(
            $this->renderExceptionContent($e),
            $this->normalizeStatusCode($e),
            $this->normalizeHeaders($e)
        );
    }

    /**
     * 取得异常默认渲染.
     */
    protected function renderExceptionContent(Throwable $e): string
    {
        if ($this->app->isDebug()) {
            return $this->renderExceptionWithWhoops($e);
        }

        return $this->renderExceptionWithDefault($e);
    }

    /**
     * 默认异常渲染.
     */
    protected function renderExceptionWithDefault(Throwable $e): string
    {
        $vars = $this->getExceptionVars($e);

        return $this->renderWithFile($this->getDefaultHttpExceptionView(), $vars);
    }

    /**
     * Whoops 渲染异常.
     */
    protected function renderExceptionWithWhoops(Throwable $e): string
    {
        $whoops = $this->makeWhoops();
        $prettyPage = new PrettyPageHandler();
        $prettyPage->handleUnconditionally(true);
        $whoops->pushHandler($prettyPage);

        return $whoops->handleException($e);
    }

    /**
     * 获取异常格式化变量.
     */
    protected function getExceptionVars(Throwable $e): array
    {
        return [
            'e'           => $e,
            'status_code' => $this->normalizeStatusCode($e),
            'code'        => $e->getCode(),
            'message'     => $e->getMessage(),
            'type'        => get_class($e),
            'file'        => $this->filterPhysicalPath($e->getFile()),
            'line'        => $e->getLine(),
        ];
    }

    /**
     * 格式化 HTTP 状态码.
     */
    protected function normalizeStatusCode(Throwable $e): int
    {
        return $this->isHttpException($e) ? $e->getStatusCode() : 500;
    }

    /**
     * 格式化响应头.
     */
    protected function normalizeHeaders(Throwable $e): array
    {
        return $this->isHttpException($e) ? $e->getHeaders() : [];
    }

    /**
     * 创建 Whoops.
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
     */
    protected function makeJsonResponseHandler(): JsonResponseHandler
    {
        return (new JsonResponseHandler())->addTraceToOutput($this->app->isDebug());
    }

    /**
     * 准备异常.
     */
    protected function prepareException(Throwable $e): Throwable
    {
        if ($e instanceof EntityNotFoundException || $e instanceof RouterNotFoundException) {
            $e = new class($e->getMessage(), $e->getCode()) extends NotFoundHttpException {
            };
        }

        return $e;
    }

    /**
     * 是否为 HTTP 异常.
     */
    protected function isHttpException(Throwable $e): bool
    {
        return $e instanceof HttpException;
    }

    /**
     * 通过模板渲染异常.
     *
     * @throws \Exception
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
        $content = ob_get_contents() ?: '';
        ob_end_clean();

        return $content;
    }

    /**
     * 过滤物理路径.
     *
     * - 基于安全考虑.
     */
    protected function filterPhysicalPath(string $path): string
    {
        return str_replace($this->app->path().'/', '', $path);
    }
}

// import fn.
class_exists(convert_json::class);
class_exists(should_json::class);
