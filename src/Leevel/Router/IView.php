<?php

declare(strict_types=1);

namespace Leevel\Router;

use Leevel\View\IView as IViews;

/**
 * IView 接口.
 */
interface IView
{
    /**
     * 切换视图.
     */
    public function switchView(IViews $view): void;

    /**
     * 变量赋值.
     */
    public function setVar(array|string $name, mixed $value = null): void;

    /**
     * 获取变量赋值.
     */
    public function getVar(?string $name = null): mixed;

    /**
     * 删除变量值.
     */
    public function deleteVar(array $name): void;

    /**
     * 清空变量值.
     */
    public function clearVar(): void;

    /**
     * 加载视图文件.
     */
    public function display(string $file, array $vars = [], ?string $ext = null): string;
}
