<?php

declare(strict_types=1);

namespace Leevel\View;

use Leevel\Kernel\IApp;
use Leevel\Support\Manager as Managers;

/**
 * 视图管理器.
 */
class Manager extends Managers implements IView
{
    public function display(string $file, array $vars = [], ?string $ext = null): string
    {
        // @phpstan-ignore-next-line
        return $this->__call(__FUNCTION__, [$file, $vars, $ext]);
    }

    public function setVar(array|string $name, mixed $value = null): void
    {
        $this->__call(__FUNCTION__, [$name, $value]);
    }

    public function getVar(?string $name = null): mixed
    {
        return $this->__call(__FUNCTION__, [$name]);
    }

    public function deleteVar(array $name): void
    {
        $this->__call(__FUNCTION__, [$name]);
    }

    public function clearVar(): void
    {
        $this->__call(__FUNCTION__, []);
    }

    public function connect(?string $connect = null, bool $newConnect = false, ...$arguments): IView
    {
        return parent::connect($connect, $newConnect, ...$arguments);
    }

    public function reconnect(?string $connect = null, ...$arguments): IView
    {
        return parent::reconnect($connect);
    }

    public function normalizeConnectConfig(string $connect): array
    {
        return array_merge(
            $this->getViewConfigCommon(),
            parent::normalizeConnectConfig($connect),
        );
    }

    /**
     * 取得配置命名空间.
     */
    protected function getConfigNamespace(): string
    {
        return 'view';
    }

    /**
     * 创建 html 模板驱动.
     */
    protected function makeConnectHtml(string $connect, ?string $driverClass = null): Html
    {
        $driverClass = $this->getDriverClass(Html::class, $driverClass);

        /** @var Html $html */
        $html = new $driverClass($this->normalizeConnectConfig($connect));
        $html->setParseResolver(static function (): Parser {
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

        return new $driverClass($this->normalizeConnectConfig($connect));
    }

    /**
     * 视图公共配置.
     */
    protected function getViewConfigCommon(): array
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
        // @phpstan-ignore-next-line
        return $this->container->make('app');
    }
}
