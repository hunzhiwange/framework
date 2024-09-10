<?php

declare(strict_types=1);

namespace Leevel\I18n\Proxy;

use Leevel\Di\Container;
use Leevel\I18n\I18n as BaseI18n;

/**
 * 代理 i18n.
 */
class I18n
{
    /**
     * 代理服务.
     */
    public static function proxy(): BaseI18n
    {
        // @phpstan-ignore-next-line
        return Container::singletons()->make('i18n');
    }
}
