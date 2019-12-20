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

namespace Leevel\Router\Console;

use InvalidArgumentException;
use Leevel\Console\Argument;
use Leevel\Console\Make;
use Leevel\Console\Option;
use Leevel\Router\IRouter;
use function Leevel\Support\Str\camelize;
use Leevel\Support\Str\camelize;

/**
 * 生成控制器.
 */
class Controller extends Make
{
    /**
     * 命令名字.
     *
     * @var string
     */
    protected string $name = 'make:controller';

    /**
     * 命令描述.
     *
     * @var string
     */
    protected string $description = 'Create a new controller';

    /**
     * 命令帮助.
     *
     * @var string
     */
    protected string $help = <<<'EOF'
        The <info>%command.name%</info> command to make controller with app namespace:
        
          <info>php %command.full_name% name action</info>
        
        You can also by using the <comment>--namespace</comment> option:
        
          <info>php %command.full_name% name action --namespace=common</info>

        You can also by using the <comment>--stub</comment> option:
        
          <info>php %command.full_name% name --stub=/stub/controller</info>

        You can also by using the <comment>--subdir</comment> option:
        
          <info>php %command.full_name% name --subdir=foo/bar</info>
        EOF;

    /**
     * 响应命令.
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
        $saveFilePath = $this->parseSaveFilePath($controllerNamespace, $controller);
        $this->setSaveFilePath($saveFilePath);

        // 设置类型
        $this->setMakeType('controller');

        // 执行
        $this->create();
    }

    /**
     * 设置自定义替换 KEY.
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
     */
    protected function parseSaveFilePath(string $controllerNamespace, string $controller): string
    {
        return $this->getNamespacePath().
            str_replace('\\', '/', $controllerNamespace).'/'.
            ($this->option('subdir') ? $this->normalizeSubdir($this->option('subdir')) : '').
            $controller.'.php';
    }

    /**
     * 获取控制器.
     */
    protected function parseController(): string
    {
        return ucfirst(camelize($this->argument('name')));
    }

    /**
     * 获取方法.
     */
    protected function parseAction(): string
    {
        return $this->normalizeAction($this->argument('action'));
    }

    /**
     * 获取模板路径.
     *
     * @throws \InvalidArgumentException
     */
    protected function getStubPath(): string
    {
        if ($this->option('stub')) {
            $stub = $this->option('stub');
        } else {
            $stub = __DIR__.'/stub/controller';
        }

        if (!is_file($stub)) {
            $e = sprintf('Controller stub file `%s` was not found.', $stub);

            throw new InvalidArgumentException($e);
        }

        return $stub;
    }

    /**
     * 格式化方法名.
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
     */
    protected function getArguments(): array
    {
        return [
            [
                'name',
                Argument::REQUIRED,
                'This is the controller name.',
            ],
            [
                'action',
                Argument::OPTIONAL,
                'This is the action name.',
                'index',
            ],
        ];
    }

    /**
     * 命令配置.
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
            [
                'subdir',
                null,
                Option::VALUE_OPTIONAL,
                'Subdir of controller',
            ],
        ];
    }
}

// import fn.
class_exists(camelize::class);
