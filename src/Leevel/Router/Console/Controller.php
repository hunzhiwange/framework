<?php

declare(strict_types=1);

namespace Leevel\Router\Console;

use Leevel\Console\Make;
use Leevel\Router\IRouter;
use Leevel\Support\Str\Camelize;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;

/**
 * 生成控制器.
 */
class Controller extends Make
{
    /**
     * 命令名字.
     */
    protected string $name = 'make:controller';

    /**
     * 命令描述.
     */
    protected string $description = 'Create a new controller';

    /**
     * 命令帮助.
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
    public function handle(IRouter $router): int
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

        return 0;
    }

    /**
     * 设置自定义替换 KEY.
     */
    protected function setCustomReplace(string $controllerNamespace, string $controller, string $action): void
    {
        $this->setCustomReplaceKeyValue('controller_dir', $controllerNamespace);
        $this->setCustomReplaceKeyValue('file_name', $controller);
        $this->setCustomReplaceKeyValue('file_title', $controller);
        $this->setCustomReplaceKeyValue('controller', $controller);
        $this->setCustomReplaceKeyValue('action', $action);
        $this->setCustomReplaceKeyValue('sub_dir', $this->normalizeSubDir($this->getOption('subdir'), true));
    }

    /**
     * 获取保存文件路径.
     */
    protected function parseSaveFilePath(string $controllerNamespace, string $controller): string
    {
        return $this->getNamespacePath().
            ($controllerNamespace ? str_replace('\\', '/', $controllerNamespace).'/' : '').
            $this->normalizeSubDir($this->getOption('subdir')).
            $controller.'.php';
    }

    /**
     * 获取控制器.
     */
    protected function parseController(): string
    {
        return ucfirst(Camelize::handle((string) $this->getArgument('name')));
    }

    /**
     * 获取方法.
     */
    protected function parseAction(): string
    {
        return $this->normalizeAction($this->getArgument('action'));
    }

    /**
     * 获取模板路径.
     *
     * @throws \InvalidArgumentException
     */
    protected function getStubPath(): string
    {
        if ($this->getOption('stub')) {
            $stub = (string) $this->getOption('stub');
        } else {
            $stub = __DIR__.'/stub/controller';
        }

        if (!is_file($stub)) {
            $e = sprintf('Controller stub file `%s` was not found.', $stub);

            throw new \InvalidArgumentException($e);
        }

        return $stub;
    }

    /**
     * 格式化方法名.
     */
    protected function normalizeAction(string $action): string
    {
        if (str_contains($action, '-')) {
            $action = str_replace('-', '_', $action);
        }

        if (str_contains($action, '_')) {
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
                InputArgument::REQUIRED,
                'This is the controller name.',
            ],
            [
                'action',
                InputArgument::OPTIONAL,
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
                InputOption::VALUE_OPTIONAL,
                'Apps namespace registered to system,default namespace is these (App,Admin)',
                'App',
            ],
            [
                'stub',
                null,
                InputOption::VALUE_OPTIONAL,
                'Custom stub of entity',
            ],
            [
                'subdir',
                null,
                InputOption::VALUE_OPTIONAL,
                'Subdir of controller',
            ],
        ];
    }
}
