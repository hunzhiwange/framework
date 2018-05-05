<?php declare(strict_types=1);
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

use Exception;
use Leevel\Console\Load;
use Leevel\Http\Request;
use Leevel\Console\Application;
use Leevel\Support\Debug\Console;
use Leevel\Bootstrap\Bootstrap\{
    LoadI18n,
    LoadOption,
    RegisterRuntime,
    TraverseProvider
};
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * 命令行内核执行
 *
 * @author Xiangmin Liu <635750556@qq.com>
 * @package $$
 * @since 2018.05.04
 * @version 1.0
 */
abstract class KernelConsole implements IKernelConsole
{

    /**
     * 项目
     *
     * @var \Leevel\Bootstrap\IProject
     */
    protected $project;

    /**
     * 项目初始化执行
     *
     * @var array
     */
    protected $bootstraps = [
        LoadOption::class,
        LoadI18n::class,
        RegisterRuntime::class,
        TraverseProvider::class
    ];

    /**
     * 构造函数
     *
     * @param \Leevel\Bootstrap\IProject $project
     * @return void
     */
    public function __construct(IProject $project)
    {
        $this->project = $project;
    }

    /**
     * 响应命令行请求
     *
     * @param \Symfony\Component\Console\Input\InputInterface $input
     * @param \Symfony\Component\Console\Output\OutputInterface $output
     * @return int
     */
    public function handle(InputInterface $input = null, OutputInterface $output = null)
    {
        $this->registerBaseService();

        $this->bootstrap();

        $this->loadCommands();

        return $this->getConsoleApplication()->run($input, $output);
    }

    /**
     * 执行结束
     *
     * @param int $status
     * @param \Symfony\Component\Console\Input\InputInterface $input
     * @return void
     */
    public function terminate(int $status, InputInterface $input = null)
    {
    }

    /**
     * 返回项目
     *
     * @return \Leevel\Bootstrap\IProject
     */
    public function getProject()
    {
        return $this->project;
    }

    /**
     * 取得命令行应用
     *
     * @return \Leevel\Console\Application
     */
    protected function getConsoleApplication()
    {
        return $this->project->make('leevel');
    }

    /**
     * 注册基础服务
     *
     * @return void
     */
    protected function registerBaseService()
    {
        $this->project->instance('request', Request::createFromGlobals());

        $this->project->instance('leevel', new Application($this->project, $this->project->version()));

        $this->project->singleton('console.load', function () {
            return new Load();
        });
    }

   /**
     * 初始化
     *
     * @return void
     */
    protected function bootstrap()
    {
       $this->project->bootstrap($this->bootstraps);
    }

    /**
     * 载入
     *
     * @return void
     */
    protected function loadCommands()
    {
        $namespaces = $this->getSystemCommandNamespaces();

        $namespaces = $this->project->getPathByNamespaces($namespaces);

        $this->project['console.load']->addNamespace($namespaces);

        $this->getConsoleApplication()->normalizeCommands($this->project['console.load']->loadData());
    }

    /**
     * 获取系统命令
     *
     * @return array
     */
    protected function getSystemCommandNamespaces()
    {
        return [
            'Leevel\Database\Console',
            'Leevel\I18n\Console',
            'Leevel\Mvc\Console',
            'Leevel\Queue\Console',
            'Leevel\Router\Console',
            'Leevel\Swoole\Console',
        ];
    }
}
