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
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * 命令行内核执行.
 */
abstract class KernelConsole implements IKernelConsole
{
    /**
     * 应用.
     *
     * @var \Leevel\Kernel\IApp
     */
    protected IApp $app;

    /**
     * 命令行应用.
     *
     * @var \Leevel\Console\Application
     */
    protected ?Application $consoleApplication = null;

    /**
     * 应用初始化执行.
     *
     * @var array
     */
    protected array $bootstraps = [
        LoadOption::class,
        LoadI18n::class,
        RegisterExceptionRuntime::class,
        TraverseProvider::class,
    ];

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
     * 响应命令行请求.
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
     * 执行结束.
     */
    public function terminate(int $status, ?InputInterface $input = null): void
    {
    }

    /**
     * 初始化.
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
     * 取得命令行应用.
     *
     * @codeCoverageIgnore
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
            ->instance('request', Request::createFromGlobals());
        $this->app
            ->container()
            ->alias('request', Request::class);
    }

    /**
     * 载入.
     */
    protected function loadCommands(): void
    {
        $commands = $this->normalizeCommands($this->getCommands());
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
        if (class_exists('Phinx\\Console\\Command\\Test')) {
            return $commands;
        }

        $warningMessage = 'Phinx is invalid,it belongs to development dependence.'.PHP_EOL.
            'You can execute `composer dump-autoload --optimize` to make it ok.';
        fwrite(STDOUT, $warningMessage.PHP_EOL);

        $invalidCommands = [
            Breakpoint::class, Create::class, Migrate::class,
            Rollback::class, SeedCreate::class, SeedRun::class,
            Status::class, Test::class,
        ];
        foreach ($commands as $k => $v) {
            if (in_array($v, $invalidCommands, true)) {
                unset($commands[$k]);
            }
        }

        return array_values($commands);
    }

    /**
     * 设置全局替换.
     */
    protected function setGlobalReplace(): void
    {
        $replace = $this->app
            ->container()
            ->make('option')
            ->get('console\\template') ?: [];
        Make::setGlobalReplace($replace);
    }

    /**
     * 整理命令.
     */
    protected function normalizeCommands(array $commands): array
    {
        $result = $tmp = [];
        foreach ($commands as $item) {
            $tmp[class_exists($item) ? 'class' : 'namespace'][] = $item;
        }

        if (isset($tmp['class'])) {
            $result = $tmp['class'];
        }

        if (isset($tmp['namespace'])) {
            $result = array_merge($result, $this->getCommandsWithNamespace($tmp['namespace']));
        }

        return $result;
    }

    /**
     * 整理命令.
     */
    protected function getCommandsWithNamespace(array $namespaces): array
    {
        $data = [];
        foreach ($namespaces as $item) {
            $data[$item] = $this->app->namespacePath($item.'\\Index');
        }

        return (new Load())
            ->addNamespace($data)
            ->loadData();
    }

    /**
     * 获取系统命令.
     */
    protected function getCommands(): array
    {
        return $this->app
            ->container()
            ->make('option')
            ->get(':composer.commands');
    }
}
