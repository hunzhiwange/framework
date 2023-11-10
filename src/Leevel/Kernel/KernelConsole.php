<?php

declare(strict_types=1);

namespace Leevel\Kernel;

use Leevel\Console\Application;
use Leevel\Console\Load;
use Leevel\Console\Make;
use Leevel\Database\Console\Breakpoint;
use Leevel\Database\Console\Create;
use Leevel\Database\Console\Migrate;
use Leevel\Database\Console\Rollback;
use Leevel\Database\Console\SeedCreate;
use Leevel\Database\Console\SeedRun;
use Leevel\Database\Console\Status;
use Leevel\Database\Console\Test;
use Leevel\Http\Request;
use Leevel\Kernel\Bootstrap\LoadI18n;
use Leevel\Kernel\Bootstrap\LoadOption;
use Leevel\Kernel\Bootstrap\RegisterExceptionRuntime;
use Leevel\Kernel\Bootstrap\TraverseProvider;
use Leevel\Option\IOption;
use Symfony\Component\Console\Input\InputInterface;
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
        LoadOption::class,
        LoadI18n::class,
        RegisterExceptionRuntime::class,
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
    }

    /**
     * {@inheritDoc}
     */
    public function handle(?InputInterface $input = null, ?OutputInterface $output = null): int
    {
        $this->registerBaseService();
        $this->bootstrap();
        $this->setGlobalReplace();
        $this->loadCommands();

        return $this->getConsoleApplication()->run($input, $output);
    }

    /**
     * {@inheritDoc}
     */
    public function terminate(int $status, ?InputInterface $input = null): void
    {
    }

    /**
     * {@inheritDoc}
     */
    public function bootstrap(): void
    {
        $this->app->bootstrap(array_merge($this->bootstraps, $this->extendBootstraps));
    }

    /**
     * {@inheritDoc}
     */
    public function getApp(): IApp
    {
        return $this->app;
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
        $commands = $this->clearInvalidCommands($commands);
        $this->getConsoleApplication()->normalizeCommands($commands);
    }

    /**
     * 清理无效命令.
     *
     * - 数据库迁移相关命令在生产环境执行 `composer dump-autoload --optimize --no-dev` 后失效，需要清理掉
     * - 开发阶段执行 `composer dump-autoload --optimize` 命令后可用
     */
    protected function clearInvalidCommands(array $commands): array
    {
        if (!$this->includeInvalidCommands()) {
            return $commands;
        }

        $invalidCommands = [
            Breakpoint::class,
            Create::class,
            Migrate::class,
            Rollback::class,
            SeedCreate::class,
            SeedRun::class,
            Status::class,
            Test::class,
        ];
        foreach ($commands as $k => $v) {
            if (\in_array($v, $invalidCommands, true)) {
                unset($commands[$k]);
            }
        }

        return array_values($commands);
    }

    protected function includeInvalidCommands(): bool
    {
        return !class_exists('Phinx\\Console\\Command\\Test');
    }

    /**
     * 设置全局替换.
     */
    protected function setGlobalReplace(): void
    {
        /** @var IOption $option */
        $option = $this->app
            ->container()
            ->make('option')
        ;
        $replace = (array) $option->get('console\\template', []) ?: [];
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

        return (new Load())
            ->addNamespace($data)
            ->loadData()
        ;
    }

    /**
     * 获取系统命令命名空间.
     */
    protected function getCommandNamespaces(): array
    {
        /** @var IOption $option */
        $option = $this->app
            ->container()
            ->make('option')
        ;

        return (array) $option->get(':composer.commands', []) ?: [];
    }
}
