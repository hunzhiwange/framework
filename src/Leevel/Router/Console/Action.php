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
 * (c) 2010-2019 http://queryphp.com All rights reserved.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Leevel\Router\Console;

use InvalidArgumentException;
use Leevel\Console\Argument;
use Leevel\Console\Make;
use Leevel\Console\Option;
use Leevel\Router\IRouter;
use function Leevel\Support\Str\camelize;
use Leevel\Support\Str\camelize;

/**
 * 生成方法器.
 *
 * @author Xiangmin Liu <635750556@qq.com>
 *
 * @since 2017.05.03
 *
 * @version 1.0
 */
class Action extends Make
{
    /**
     * 命令名字.
     *
     * @var string
     */
    protected $name = 'make:action';

    /**
     * 命令描述.
     *
     * @var string
     */
    protected $description = 'Create a new action.';

    /**
     * 命令帮助.
     *
     * @var string
     */
    protected $help = <<<'EOF'
        The <info>%command.name%</info> command to make action with app namespace:
        
          <info>php %command.full_name% controller name</info>
        
        You can also by using the <comment>--namespace</comment> option:
        
          <info>php %command.full_name% controller name --namespace=common</info>

        You can also by using the <comment>--stub</comment> option:
        
          <info>php %command.full_name% name --stub=/stub/action</info>
        EOF;

    /**
     * 响应命令.
     *
     * @param \Leevel\Router\IRouter $router
     */
    public function handle(IRouter $router): void
    {
        // 处理命名空间路径
        $this->parseNamespace();

        // 设置模板路径
        $this->setTemplatePath($this->getStubPath());

        // 关键参数
        $controllerNamespace = $router->getControllerDir();
        $controller = $this->parseController();
        $action = $this->parseAction();

        // 设置自定义替换 KEY
        $this->setCustomReplace($controllerNamespace, $controller, $action);

        // 保存路径
        $saveFilePath = $this->parseSaveFilePath($controllerNamespace, $controller, $action);
        $this->setSaveFilePath($saveFilePath);

        // 设置类型
        $this->setMakeType('action');

        // 执行
        $this->create();
    }

    /**
     * 设置自定义替换 KEY.
     *
     * @param string $controllerNamespace
     * @param string $controller
     * @param string $action
     */
    protected function setCustomReplace(string $controllerNamespace, string $controller, string $action): void
    {
        $this->setCustomReplaceKeyValue('controller_dir', $controllerNamespace);
        $this->setCustomReplaceKeyValue('file_name', $controller);
        $this->setCustomReplaceKeyValue('controller', $controller);
        $this->setCustomReplaceKeyValue('action', $action);
    }

    /**
     * 获取保存文件路径.
     *
     * @param string $controllerNamespace
     * @param string $controller
     * @param string $action
     *
     * @return string
     */
    protected function parseSaveFilePath(string $controllerNamespace, string $controller, string $action): string
    {
        return $this->getNamespacePath().
            str_replace('\\', '/', $controllerNamespace).'/'.
            $controller.'/'.$action.'.php';
    }

    /**
     * 获取控制器.
     *
     * @return string
     */
    protected function parseController(): string
    {
        return ucfirst(camelize($this->argument('controller')));
    }

    /**
     * 获取方法.
     *
     * @return string
     */
    protected function parseAction(): string
    {
        return ucfirst($this->normalizeAction($this->argument('name')));
    }

    /**
     * 获取模板路径.
     *
     * @throws \InvalidArgumentException
     *
     * @return string
     */
    protected function getStubPath(): string
    {
        if ($this->option('stub')) {
            $stub = $this->option('stub');
        } else {
            $stub = __DIR__.'/stub/action';
        }

        if (!is_file($stub)) {
            $e = sprintf('Action stub file `%s` was not found.', $stub);

            throw new InvalidArgumentException($e);
        }

        return $stub;
    }

    /**
     * 格式化方法名.
     *
     * @param string $action
     *
     * @return string
     */
    protected function normalizeAction(string $action): string
    {
        if (false !== strpos($action, '-')) {
            $action = str_replace('-', '_', $action);
        }

        if (false !== strpos($action, '_')) {
            $action = '_'.str_replace('_', ' ', $action);
            $action = ltrim(str_replace(' ', '', ucwords($action)), '_');
        }

        return $action;
    }

    /**
     * 命令参数.
     *
     * @return array
     */
    protected function getArguments(): array
    {
        return [
            [
                'controller',
                Argument::OPTIONAL,
                'This is the parent controller name.',
            ],
            [
                'name',
                Argument::OPTIONAL,
                'This is the action name.',
            ],
        ];
    }

    /**
     * 命令配置.
     *
     * @return array
     */
    protected function getOptions(): array
    {
        return [
            [
                'namespace',
                null,
                Option::VALUE_OPTIONAL,
                'Apps namespace registered to system,default namespace is these (Common,App,Admin)',
                'app',
            ],
            [
                'stub',
                null,
                Option::VALUE_OPTIONAL,
                'Custom stub of entity',
            ],
        ];
    }
}

// import fn.
class_exists(camelize::class);
