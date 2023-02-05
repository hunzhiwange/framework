<?php

declare(strict_types=1);

namespace Leevel\View;

use Leevel\Kernel\IApp;
use Leevel\Support\Manager as Managers;

/**
 * 视图管理器.
 *
 * @method static string                display(string $file, array $vars = [], ?string $ext = null) 加载视图文件.
 * @method static void                  setParseResolver(\Closure $parseResolver)                    设置 parser 解析回调.
 * @method static string                getCachePath(string $file)                                   获取编译路径.
 * @method static void                  setVar($name, $value = null)                                 设置模板变量.
 * @method        static                getVar(?string $name = null)                                 获取变量值.
 * @method static void                  deleteVar(array $name)                                       删除变量值.
 * @method static void                  clearVar()                                                   清空变量值.
 * @method static \Leevel\Di\IContainer container()                                                  返回 IOC 容器.
 * @method static void                  disconnect(?string $connect = null)                          删除连接.
 * @method static array                 getConnects()                                                取回所有连接.
 * @method static string                getDefaultConnect()                                          返回默认连接.
 * @method static void                  setDefaultConnect(string $name)                              设置默认连接.
 * @method static mixed                 getContainerOption(?string $name = null)                     获取容器配置值.
 * @method static void                  setContainerOption(string $name, mixed $value)               设置容器配置值.
 * @method static void                  extend(string $connect, \Closure $callback)                  扩展自定义连接.
 */
class Manager extends Managers
{
    /**
     * {@inheritDoc}
     */
    public function connect(?string $connect = null, bool $newConnect = false): IView
    {
        return parent::connect($connect, $newConnect);
    }

    /**
     * {@inheritDoc}
     */
    public function reconnect(?string $connect = null): IView
    {
        return parent::reconnect($connect);
    }

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
    protected function makeConnectHtml(string $connect, ?string $driverClass = null): Html
    {
        $driverClass = $this->getDriverClass(Html::class, $driverClass);
        $html = new $driverClass($this->normalizeConnectOption($connect));
        $html->setParseResolver(function (): Parser {
            return (new Parser(new Compiler()))
                ->registerCompilers()
                ->registerParsers()
            ;
        });

        return $html;
    }

    /**
     * 创建 phpui 模板驱动.
     */
    protected function makeConnectPhpui(string $connect, ?string $driverClass = null): Phpui
    {
        $driverClass = $this->getDriverClass(Phpui::class, $driverClass);

        return new $driverClass($this->normalizeConnectOption($connect));
    }

    /**
     * 视图公共配置.
     */
    protected function getViewOptionCommon(): array
    {
        $app = $this->getApp();

        return [
            'theme_path' => $app->themesPath(),
            'cache_path' => $app->storagePath('app/themes'),
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
