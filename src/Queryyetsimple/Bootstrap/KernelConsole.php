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
 * (c) 2010-2018 http://queryphp.com All rights reserved.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Leevel\Bootstrap;

use Leevel\Bootstrap\Bootstrap\LoadI18n;
use Leevel\Bootstrap\Bootstrap\LoadOption;
use Leevel\Bootstrap\Bootstrap\RegisterRuntime;
use Leevel\Bootstrap\Bootstrap\TraverseProvider;
use Leevel\Console\Application;
use Leevel\Console\Load;
use Leevel\Console\Make;
use Leevel\Http\Request;
use Leevel\Kernel\IKernelConsole;
use Leevel\Kernel\IProject;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * 命令行内核执行.
 *
 * @author Xiangmin Liu <635750556@qq.com>
 *
 * @since 2018.05.04
 *
 * @version 1.0
 */
abstract class KernelConsole implements IKernelConsole
{
    /**
     * 项目.
     *
     * @var \Leevel\Kernel\IProject
     */
    protected $project;

    /**
     * 命令行应用.
     *
     * @var \Leevel\Console\Application
     */
    protected $consoleApplication;

    /**
     * 项目初始化执行.
     *
     * @var array
     */
    protected $bootstraps = [
        LoadOption::class,
        LoadI18n::class,
        RegisterRuntime::class,
        TraverseProvider::class,
    ];

    /**
     * 构造函数.
     *
     * @param \Leevel\Kernel\IProject $project
     */
    public function __construct(IProject $project)
    {
        $this->project = $project;
    }

    /**
     * 响应命令行请求
     *
     * @param \Symfony\Component\Console\Input\InputInterface   $input
     * @param \Symfony\Component\Console\Output\OutputInterface $output
     *
     * @return int
     */
    public function handle(InputInterface $input = null, OutputInterface $output = null): int
    {
        $this->registerBaseService();

        $this->bootstrap();

        $this->setGlobalReplace();

        $this->loadCommands();

        return $this->getConsoleApplication()->run($input, $output);
    }

    /**
     * 执行结束
     *
     * @param int                                             $status
     * @param \Symfony\Component\Console\Input\InputInterface $input
     * @codeCoverageIgnore
     */
    public function terminate(int $status, InputInterface $input = null): void
    {
    }

    /**
     * 返回项目.
     *
     * @return \Leevel\Kernel\IProject
     */
    public function getProject(): IProject
    {
        return $this->project;
    }

    /**
     * 取得命令行应用.
     *
     * @return \Leevel\Console\Application
     * @codeCoverageIgnore
     */
    protected function getConsoleApplication(): Application
    {
        if ($this->consoleApplication) {
            return $this->consoleApplication;
        }

        return $this->consoleApplication = new Application($this->project, $this->project->version());
    }

    /**
     * 注册基础服务
     */
    protected function registerBaseService(): void
    {
        $this->project->instance('request', Request::createFromGlobals());
    }

    /**
     * 初始化.
     *
     * @codeCoverageIgnore
     */
    protected function bootstrap(): void
    {
        $this->project->bootstrap($this->bootstraps);
    }

    /**
     * 载入.
     */
    protected function loadCommands(): void
    {
        $commands = $this->normalizeCommands($this->getCommands());

        $this->getConsoleApplication()->normalizeCommands($commands);
    }

    /**
     * 设置全局替换.
     */
    protected function setGlobalReplace(): void
    {
        Make::setGlobalReplace(
            $this->project['option']->get('console\\template') ?: []
        );
    }

    /**
     * 整理命令.
     *
     * @param array $commands
     *
     * @return array
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
     *
     * @param array $namespaces
     *
     * @return array
     */
    protected function getCommandsWithNamespace(array $namespaces): array
    {
        $data = [];

        foreach ($namespaces as $item) {
            $data[$item] = $this->project->getPathByComposer($item);
        }

        return (new Load())->addNamespace($data)->loadData();
    }

    /**
     * 获取系统命令.
     *
     * @return array
     */
    protected function getCommands(): array
    {
        return $this->project['option']->get('_composer.commands');
    }
}
