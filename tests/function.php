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
    public function gettext(string $text, ...$data): string
    {
        return sprintf($text, ...$data);
    }
}
