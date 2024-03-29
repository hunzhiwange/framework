<?php

declare(strict_types=1);

namespace Leevel\Kernel\Exceptions;

use Leevel\Database\Ddd\DataNotFoundException;
use Leevel\Database\Ddd\EntityNotFoundException;
use Leevel\Http\JsonResponse;
use Leevel\Http\Request;
use Leevel\Kernel\IApp;
use Leevel\Kernel\Inspector;
use Leevel\Log\ILog;
use Leevel\Router\RouterNotFoundException;
use Leevel\Support\Arr\ConvertJson;
use Leevel\Support\Arr\ShouldJson;
use NunoMaduro\Collision\Provider as CollisionProvider;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\HttpFoundation\Response;
use Whoops\Handler\JsonResponseHandler;
use Whoops\Handler\PrettyPageHandler;
use Whoops\Run;

/**
 * 异常运行时.
 */
abstract class Runtime implements IRuntime
{
    /**
     * 构造函数.
     */
    public function __construct(protected IApp $app)
    {
    }

    /**
     * {@inheritDoc}
     */
    public function report(\Throwable $e): void
    {
        if (!$this->reportable($e)) {
            return;
        }

        if (method_exists($e, 'report')) {
            $e->report();

            return;
        }

        $this->reportToLog($e);
    }

    /**
     * {@inheritDoc}
     */
    public function reportable(\Throwable $e): bool
    {
        if (method_exists($e, 'reportable') && false === $e->reportable()) {
            return false;
        }

        return true;
    }

    /**
     * {@inheritDoc}
     */
    public function render(Request $request, \Throwable $e): Response
    {
        if (method_exists($e, 'render') && $response = $e->render($request, $e)) {
            if (!$response instanceof Response) {
                if (ShouldJson::handle($response)) {
                    $response = JsonResponse::fromJsonString(
                        ConvertJson::handle($response, JSON_UNESCAPED_UNICODE),
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
     * {@inheritDoc}
     */
    public function renderForConsole(OutputInterface $output, \Throwable $e): void
    {
        $handler = (new CollisionProvider())
            ->register()
            ->getHandler()
            ->setOutput($output)
        ;
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
     * 获取 JSON 状态的异常模板.
     */
    abstract public function getJsonExceptionView(HttpException $e): string;

    /**
     * 获取 JSON 状态的默认异常结果.
     */
    abstract public function getDefaultJsonExceptionData(\Throwable $e): array;

    /**
     * 记录异常到日志.
     */
    protected function reportToLog(\Throwable $e): void
    {
        try {
            /** @var ILog $log */
            $log = $this->app->container()->make(ILog::class);
            $log->error($e->getMessage(), ['exception' => (string) $e]);
        } catch (\Throwable) {
        }
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
    protected function makeHttpResponse(\Throwable $e): Response
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
    protected function makeJsonResponse(\Throwable $e): Response
    {
        if ($this->app->isDebug()) {
            $whoops = $this->makeWhoops();
            $whoops->pushHandler($this->makeJsonResponseHandler());
            $data = $whoops->handleException($e);
            $data = json_decode($data, true);
            $data['code'] = $e->getCode();
            if ($this->isHttpException($e) && isset($data['error'])) {
                // @phpstan-ignore-next-line
                $data['error']['duration'] = $e->getDuration();
            }
        } else {
            if ($this->isHttpException($e) && file_exists($filepath = $this->getJsonExceptionView($e))) {
                $data = $this->renderJsonWithFile($filepath, array_merge($this->getExceptionVars($e), ['shouldJson' => true]));
            } else {
                $data = $this->getDefaultJsonExceptionData($e);
            }
        }

        return JsonResponse::fromJsonString(
            json_encode($data, JSON_UNESCAPED_UNICODE) ?: '',
            $this->normalizeStatusCode($e),
            $this->normalizeHeaders($e)
        );
    }

    /**
     * 异常创建响应.
     */
    protected function convertExceptionToResponse(\Throwable $e): Response
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
    protected function renderExceptionContent(\Throwable $e): string
    {
        if ($this->app->isDebug()) {
            return $this->renderExceptionWithWhoops($e);
        }

        return $this->renderExceptionWithDefault($e);
    }

    /**
     * 默认异常渲染.
     */
    protected function renderExceptionWithDefault(\Throwable $e): string
    {
        $vars = $this->getExceptionVars($e);

        return $this->renderWithFile($this->getDefaultHttpExceptionView(), $vars);
    }

    /**
     * Whoops 渲染异常.
     */
    protected function renderExceptionWithWhoops(\Throwable $e): string
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
    protected function getExceptionVars(\Throwable $e): array
    {
        $data = [
            'e' => $e,
            'statusCode' => $this->normalizeStatusCode($e),
            'code' => $e->getCode(),
            'message' => $e->getMessage(),
            'type' => $e::class,
            'file' => $e->getFile(),
            'line' => $e->getLine(),
        ];

        if ($this->isHttpException($e)) {
            // @phpstan-ignore-next-line
            $data['duration'] = $e->getDuration();
            // @phpstan-ignore-next-line
            $data['errorBlocking'] = $e->getErrorBlocking();
        }

        return $data;
    }

    /**
     * 格式化 HTTP 状态码.
     */
    protected function normalizeStatusCode(\Throwable $e): int
    {
        // @phpstan-ignore-next-line
        return $this->isHttpException($e) ? $e->getStatusCode() : 500;
    }

    /**
     * 格式化响应头.
     */
    protected function normalizeHeaders(\Throwable $e): array
    {
        // @phpstan-ignore-next-line
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
        // @phpstan-ignore-next-line
        return (new JsonResponseHandler())->addTraceToOutput(true);
    }

    /**
     * 准备异常.
     */
    protected function prepareException(\Throwable $e): \Throwable
    {
        if ($e instanceof EntityNotFoundException
            || $e instanceof RouterNotFoundException
            || $e instanceof DataNotFoundException) {
            $e = new class($e->getMessage(), $e->getCode()) extends NotFoundHttpException {
            };
        }

        return $e;
    }

    /**
     * 是否为 HTTP 异常.
     */
    protected function isHttpException(\Throwable $e): bool
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
            throw new \Exception(sprintf('Exception file %s is not exist.', $filepath));
        }

        extract($vars);

        ob_start();

        require $filepath;
        $content = ob_get_contents() ?: '';
        ob_end_clean();

        return $content;
    }

    /**
     * 通过模板渲染异常（JSON）.
     *
     * @throws \Exception
     */
    protected function renderJsonWithFile(string $filepath, array $vars = []): array
    {
        if (!is_file($filepath)) {
            throw new \Exception(sprintf('Exception file %s is not exist.', $filepath));
        }

        extract($vars);

        return (array) require $filepath;
    }
}
