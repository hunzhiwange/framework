<?php

declare(strict_types=1);

namespace Leevel\I18n;

use Leevel\Di\Container;

/**
 * 获取语言.
 */
class Gettext
{
    public static function handle(string $text, ...$data): string // @phpstan-ignore-line
    {
        /** @var \Leevel\I18n\I18n $service */
        $service = Container::singletons()->make('i18n');
        if (!method_exists($service, 'gettext')) {
            return sprintf($text, ...$data);
        }

        return $service->gettext($text, ...$data);
    }
}
