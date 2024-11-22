<?php

declare(strict_types=1);

namespace Leevel\Kernel;

use Leevel\Config\IConfig;
use Leevel\Console\Application;
use Leevel\Console\Load;
use Leevel\Console\Make;
use Leevel\Http\Request;
use Leevel\Kernel\Bootstrap\LoadConfig;
use Leevel\Kernel\Bootstrap\LoadI18n;
use Leevel\Kernel\Bootstrap\RegisterRuntime;
use Leevel\Kernel\Bootstrap\TraverseProvider;
use Leevel\Kernel\Exceptions\IRuntime;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\ConsoleOutput;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * 命令行内核执行.
 */
abstract class KernelConsole implements IKernelConsole
{
    /**
     * 命令行应用.
     */
    protected ?Application $consoleApplication = null;

    /**
     * 应用初始化执行.
     */
    protected array $bootstraps = [
        LoadConfig::class,
        LoadI18n::class,
        RegisterRuntime::class,
        TraverseProvider::class,
    ];

    /**
     * 应用扩展初始化执行.
     */
    protected array $extendBootstraps = [];

    /**
     * 构造函数.
     */
    public function __construct(protected IApp $app)
    {
        // 如果是协程模式，请求对象注册到协程上下文
        $this->app
            ->container()
            ->addContextKeys('request')
        ;
    }

    public function handle(?InputInterface $input = null, ?OutputInterface $output = null): int
    {
        try {
            $this->registerBaseService();
            $this->bootstrap();
            $this->setGlobalReplace();
            $this->loadCommands();

            return $this->getConsoleApplication()->run($input, $output);
        } catch (\Exception $e) {
            $this->reportException($e);

            return $this->renderException($e);
        } catch (\Throwable $e) {
            $e = new \ErrorException(
                $e->getMessage(),
                $e->getCode(),
                E_ERROR,
                $e->getFile(),
                $e->getLine(),
                $e->getPrevious()
            );
            $this->reportException($e);

            return $this->renderException($e);
        }
    }

    public function terminate(int $status, ?InputInterface $input = null): void {}

    public function bootstrap(): void
    {
        $this->app->bootstrap(array_merge($this->bootstraps, $this->extendBootstraps));
    }

    public function getApp(): IApp
    {
        return $this->app;
    }

    /**
     * 渲染异常.
     */
    protected function renderException(\Exception $e): int
    {
        $this
            ->getRuntime()
            ->renderForConsole(new ConsoleOutput(), $e)
        ;

        return Command::FAILURE;
    }

    /**
     * 上报错误.
     */
    protected function reportException(\Exception $e): void
    {
        $this->getRuntime()->report($e);
    }

    /**
     * 返回运行处理器.
     */
    protected function getRuntime(): IRuntime
    {
        // @phpstan-ignore-next-line
        return $this->app
            ->container()
            ->make(IRuntime::class)
        ;
    }

    /**
     * 取得命令行应用.
     */
    protected function getConsoleApplication(): Application
    {
        if ($this->consoleApplication) {
            return $this->consoleApplication;
        }

        return $this->consoleApplication = new Application($this->app->container(), $this->app->version());
    }

    /**
     * 注册基础服务.
     */
    protected function registerBaseService(): void
    {
        $this->app
            ->container()
            ->instance('request', Request::createFromGlobals())
        ;
        $this->app
            ->container()
            ->alias('request', Request::class)
        ;
    }

    /**
     * 载入.
     */
    protected function loadCommands(): void
    {
        $commands = $this->normalizeCommands($this->getCommandNamespaces());
        $this->getConsoleApplication()->normalizeCommands($commands);
    }

    /**
     * 设置全局替换.
     */
    protected function setGlobalReplace(): void
    {
        /** @var IConfig $config */
        $config = $this->app
            ->container()
            ->make('config')
        ;
        $replace = (array) $config->get('console\\template', []) ?: [];
        Make::setGlobalReplace($replace);
    }

    /**
     * 整理命令.
     */
    protected function normalizeCommands(array $commandsNamespaces): array
    {
        $commands = [];
        $commandsNamespacesGroup = [];
        foreach ($commandsNamespaces as $item) {
            $commandsNamespacesGroup[class_exists($item) ? 'class' : 'namespace'][] = $item;
        }

        if (isset($commandsNamespacesGroup['class'])) {
            $commands = $commandsNamespacesGroup['class'];
        }

        if (isset($commandsNamespacesGroup['namespace'])) {
            $commands = array_merge($commands, $this->getCommandsWithNamespace($commandsNamespacesGroup['namespace']));
        }

        return $commands;
    }

    /**
     * 整理命令.
     */
    protected function getCommandsWithNamespace(array $namespaces): array
    {
        $data = [];
        foreach ($namespaces as $item) {
            $data[$item] = $this->app->namespacePath($item);
        }

        return new Load()
            ->addNamespace($data)
            ->loadData()
        ;
    }

    /**
     * 获取系统命令命名空间.
     */
    protected function getCommandNamespaces(): array
    {
        /** @var IConfig $config */
        $config = $this->app
            ->container()
            ->make('config')
        ;

        return (array) $config->get(':composer.commands', []) ?: [];
    }
}
