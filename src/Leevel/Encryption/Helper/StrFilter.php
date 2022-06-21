<?php

declare(strict_types=1);

namespace Leevel\Encryption\Helper;

/**
 * 字符过滤.
 */
class StrFilter
{
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
        $data = preg_replace(
            '/&amp;((#(\d{3,5}|x[a-fA-F0-9]{4}));)/',
            '&\\1',
            CustomHtmlspecialchars::handle($data)
        );
        $data = str_replace('　', '', $data);

        return $data;
    }
}
