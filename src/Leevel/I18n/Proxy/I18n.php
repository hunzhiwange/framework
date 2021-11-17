<?php

declare(strict_types=1);

namespace Leevel\I18n\Proxy;

use Leevel\Di\Container;
use Leevel\I18n\I18n as BaseI18n;

/**
 * 代理 i18n.
 *
 * @method static string gettext(string $text, ...$data)       获取语言 text.
 * @method static void addtext(string $i18n, array $data = []) 添加语言包.
 * @method static void setI18n(string $i18n)                   设置当前语言包上下文环境.
 * @method static string getI18n()                             获取当前语言包.
 * @method static array all()                                  返回所有语言包.
 */
class I18n
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
    public static function proxy(): BaseI18n
    {
        return Container::singletons()->make('i18n');
    }
}
