<?php

declare(strict_types=1);

namespace Leevel\Kernel\Bootstrap;

use Leevel\Kernel\Exceptions\IRuntime;
use Leevel\Kernel\IApp;
use Leevel\Log\Manager;
use Symfony\Component\Console\Output\ConsoleOutput;
use Throwable;

/**
 * 注册异常运行时.
 */
class RegisterExceptionRuntime
{
    /**
     * 应用.
     */
    protected IApp $app; /** @phpstan-ignore-line */

    /**
     * 响应.
     */
    public function handle(IApp $app): void
    {
        $this->app = $app;
        $this->initialization($app->environment());
    }

    /**
     * 设置错误处理函数.
     *
     * @throws \ErrorException
     */
    public function setErrorHandle(int $code, string $description, ?string $file = null, ?int $line = null, mixed $context = null): void
    {
        // 根据 error_reporing 等级来确定是否抛出错误
        if (!(error_reporting() & $code)) {
            return;
        }

        throw new \ErrorException($description, 0, $code, (string) $file, (int) $line);
    }

    /**
     * 设置退出处理函数.
     */
    public function registerShutdownFunction(): void
    {
        if (($error = error_get_last()) && !empty($error['type'])) {
            $this->setExceptionHandler($this->formatErrorException($error));
        }
    }

    /**
     * 程序结束前写入一次日志.
     */
    public function registerShutdownFunctionForWriteLog(): void
    {
        try {
            $this->getLogManager()->flush();
        } catch (Throwable) {
            // 屏蔽掉错误
        }
    }

    /**
     * 设置异常处理函数.
     */
    public function setExceptionHandler(\Throwable $e): void
    {
        if (!$e instanceof \Exception) {
            $e = new \ErrorException(
                $e->getMessage(),
                $e->getCode(),
                E_ERROR,
                $e->getFile(),
                $e->getLine(),
                $e->getPrevious()
            );
        }

        $this->getExceptionRuntime()->report($e);

        if ($this->app->isConsole()) {
            $this->renderConsoleResponse($e);
        } else {
            $this->renderHttpResponse($e);
        }
    }

    /**
     * 初始化告警和错误处理.
     */
    protected function initialization(string $environment): void
    {
        error_reporting(E_ALL);
        // @phpstan-ignore-next-line
        set_error_handler([$this, 'setErrorHandle']);
        // @phpstan-ignore-next-line
        set_exception_handler([$this, 'setExceptionHandler']);
        register_shutdown_function([$this, 'registerShutdownFunction']);
        register_shutdown_function([$this, 'registerShutdownFunctionForWriteLog']);
        if ('production' === $environment) {
            ini_set('display_errors', 'Off');
        }
    }

    /**
     * 渲染命令行异常并输出.
     */
    protected function renderConsoleResponse(\Exception $e): void
    {
        $this
            ->getExceptionRuntime()
            ->renderForConsole(new ConsoleOutput(), $e)
        ;
    }

    /**
     * 渲染 HTTP 异常并输出.
     */
    protected function renderHttpResponse(\Exception $e): void
    {
        $request = $this->app
            ->container()
            ->make('request')
        ;

        $this
            ->getExceptionRuntime()
            ->render($request, $e)
            ->send()
        ;
    }

    /**
     * 格式化致命错误信息.
     */
    protected function formatErrorException(array $error): \ErrorException
    {
        return new \ErrorException(
            (string) $error['message'],
            (int) $error['type'],
            0,
            (string) $error['file'],
            (int) $error['line']
        );
    }

    /**
     * 返回运行处理器.
     */
    protected function getExceptionRuntime(): IRuntime
    {
        // @phpstan-ignore-next-line
        return $this->app
            ->container()
            ->make(IRuntime::class)
        ;
    }

    /**
     * 返回日志管理器.
     */
    protected function getLogManager(): Manager
    {
        // @phpstan-ignore-next-line
        return $this->app
            ->container()
            ->make(Manager::class)
        ;
    }
}
