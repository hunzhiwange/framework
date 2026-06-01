<?php

declare(strict_types=1);

/**
 * 国际化组件模拟.
 */
class I18nMock
{
    /**
     * lang.
     */
    public function gettext(string $text, bool|float|int|string|null ...$data): string
    {
        return sprintf($text, ...$data);
    }
}
