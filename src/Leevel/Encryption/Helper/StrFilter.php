<?php

declare(strict_types=1);

namespace Leevel\Encryption\Helper;

class StrFilter
{
    /**
     * 字符过滤.
     */
    public static function handle(mixed $data): mixed
    {
        if (\is_array($data)) {
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
            // @phpstan-ignore-next-line
            CustomHtmlspecialchars::handle($data)
        );

        return str_replace('　', '', $data);
    }
}
