<?php

declare(strict_types=1);

namespace Leevel\Encryption\Helper;

class HtmlFilter
{
    /**
     * HTML 过滤.
     */
    public static function handle(mixed $data): mixed
    {
        if (is_array($data)) {
            $result = [];
            foreach ($data as $key => $val) {
                $result[self::handle($key)] = self::handle($val);
            }

            return $result;
        }

        $data = trim((string) $data);
        $data = (string) preg_replace([
            '/<\s*a[^>]*href\s*=\s*[\'\"]?(javascript|vbscript)[^>]*>/i',
            '/<([^>]*)on(\w)+=[^>]*>/i',
            '/<\s*\/?\s*(script|i?frame)[^>]*\s*>/i',
        ], [
            '<a href="#">',
            '<$1>',
            '&lt;$1&gt;',
        ], $data);
        return str_replace('　', '', $data);
    }
}
