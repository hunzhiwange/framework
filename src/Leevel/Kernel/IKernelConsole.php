<?php

declare(strict_types=1);

namespace Leevel\Kernel;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * 命令行内核执行接口.
 */
interface IKernelConsole
{
    /**
     * 响应命令行请求.
     */
    public function handle(?InputInterface $input = null, ?OutputInterface $output = null): int;

    /**
     * 执行结束.
     */
    public function terminate(int $status, ?InputInterface $input = null): void;

    /**
     * 初始化.
     */
    public function bootstrap(): void;

    /**
     * 返回应用.
     */
    public function getApp(): IApp;
}
