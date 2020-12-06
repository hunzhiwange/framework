<?php

declare(strict_types=1);

namespace Leevel\I18n;

use Leevel\Di\Container;

/**
 * 获取语言.
 */
function gettext(string $text, ...$data): string
{
    /** @var \Leevel\I18n\I18n $service */
    $service = Container::singletons()->make('i18n');

    return $service->gettext($text, ...$data);
}

class gettext
{
}
