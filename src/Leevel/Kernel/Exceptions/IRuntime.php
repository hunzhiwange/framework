<?php

declare(strict_types=1);

namespace Leevel\Kernel\Exceptions;

use Leevel\Http\Request;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

/**
 * 异常运行时接口.
 */
interface IRuntime
{
    /**
     * 异常上报.
     */
    public function report(Throwable $e): void;

    /**
     * 异常是否需要上报.
     */
    public function reportable(Throwable $e): bool;

    /**
     * 异常渲染.
     */
    public function render(Request $request, Throwable $e): Response;

    /**
     * 命令行异常渲染.
     */
    public function renderForConsole(OutputInterface $output, Throwable $e): void;
}
