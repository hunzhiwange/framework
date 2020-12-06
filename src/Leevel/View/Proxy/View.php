<?php

declare(strict_types=1);

namespace Leevel\View\Proxy;

use Leevel\Di\Container;
use Leevel\View\Manager;

/**
 * 代理 view.
 *
 * @method static string display(string $file, array $vars = [], ?string $ext = null) 加载视图文件. 
 * @method static void setVar(array|string $name, mixed $value = null) 设置模板变量. 
 * @method static mixed getVar(?string $name = null) 获取变量值. 
 * @method static void deleteVar(array $name) 删除变量值. 
 * @method static void clearVar() 清空变量值. 
 */
class View
{
    /**
     * 实现魔术方法 __callStatic.
     */
    public static function __callStatic(string $method, array $args): mixed
    {
        return self::proxy()->{$method}(...$args);
    }

    /**
     * 代理服务.
     */
    public static function proxy(): Manager
    {
        return Container::singletons()->make('views');
    }
}
