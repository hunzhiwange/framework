<?php

declare(strict_types=1);

namespace Leevel\View;

/**
 * 视图接口.
 */
interface IView
{
    /**
     * 加载视图文件.
     */
    public function display(string $file, array $vars = [], ?string $ext = null): string;

    /**
     * 设置模板变量.
     */
    public function setVar(array|string $name, mixed $value = null): void;

    /**
     * 获取变量值.
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
}
