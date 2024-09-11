<?php

declare(strict_types=1);

use Leevel\Di\Container;
use Leevel\I18n\I18n;

// @codeCoverageIgnoreStart
if (!function_exists('__')) {
    /** @codeCoverageIgnoreEnd */
    /**
     * 获取语言.
     */
    function __(string $text, ...$data): string // @phpstan-ignore-line
    {
        /** @var I18n $service */
        $service = Container::singletons()->make('i18n');
        if (!method_exists($service, 'gettext')) {
            return sprintf($text, ...$data);
        }

        return $service->gettext($text, ...$data);
    }
}
