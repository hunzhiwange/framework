<?php

declare(strict_types=1);

namespace Leevel\Encryption\Helper;

class UnHtmlspecialchars
{
    /**
     * 字符 HTML 实体还原.
     */
    public static function handle(mixed $data): mixed
    {
        if (!is_array($data)) {
            $data = (array) $data;
        }

        $htmlSpecialchars = array_flip(get_html_translation_table(HTML_SPECIALCHARS));
        $data = array_map(function ($data) use ($htmlSpecialchars) {
            return strtr($data, $htmlSpecialchars);
        }, $data);

        if (1 === count($data)) {
            $data = reset($data);
        }

        return $data;
    }
}
