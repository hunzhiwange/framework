<?php

declare(strict_types=1);

namespace Leevel\View;

use Leevel\Kernel\IApp;
use Leevel\Manager\Manager as Managers;

/**
 * 视图管理器.
 *
 * @method static string display(string $file, array $vars = [], ?string $ext = null) 加载视图文件.
 * @method static void setParseResolver(\Closure $parseResolver)                      设置 parser 解析回调.
 * @method static string getCachePath(string $file)                                   获取编译路径.
 * @method static void setVar($name, $value = null)                                   设置模板变量.
 * @method static getVar(?string $name = null)                                        获取变量值.
 * @method static void deleteVar(array $name)                                         删除变量值.
 * @method static void clearVar()                                                     清空变量值.
 */
class Manager extends Managers
{
    /**
     * {@inheritDoc}
     */
    public function normalizeConnectOption(string $connect): array
    {
        return array_merge(
            $this->getViewOptionCommon(),
            parent::normalizeConnectOption($connect),
        );
    }

    /**
     * 取得配置命名空间.
     */
    protected function getOptionNamespace(): string
    {
        return 'view';
    }

    /**
     * 创建 html 模板驱动.
     */
    protected function makeConnectHtml(string $connect): Html
    {
        $html = new Html($this->normalizeConnectOption($connect));
        $html->setParseResolver(function (): Parser {
            return (new Parser( new Compiler()))
                ->registerCompilers()
                ->registerParsers();
        });

        return $html;
    }

    /**
     * 创建 phpui 模板驱动.
     */
    protected function makeConnectPhpui(string $connect): Phpui
    {
        return new Phpui($this->normalizeConnectOption($connect));
    }

    /**
     * 视图公共配置.
     */
    protected function getViewOptionCommon(): array
    {
        $app = $this->getApp();

        return [
            'theme_path' => $app->themesPath(),
            'cache_path' => $app->runtimePath('theme'),
        ];
    }

    /**
     * 获取应用.
     */
    protected function getApp(): IApp
    {
        return $this->container->make('app');
    }
}
