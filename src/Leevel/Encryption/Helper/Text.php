<?php

declare(strict_types=1);

namespace Leevel\Encryption\Helper;

class Text
{
    /**
     * 字符串文本化.
     */
    public static function handle(string $strings, bool $deep = true, array $black = []): string
    {
        if ($deep && !$black) {
            $black = [
                ' ', '&nbsp;', '&', '=', '-',
                '#', '%', '!', '@', '^', '*', 'amp;',
            ];
        }

        $strings = CleanJs::handle($strings);
        $strings = (string) preg_replace('/\s(?=\s)/', '', $strings); // 彻底过滤空格
        $strings = (string) preg_replace('/[\n\r\t]/', ' ', $strings);
        if ($black) {
            $strings = str_replace($black, '', $strings);
        }
        $strings = strip_tags($strings);
        $strings = htmlspecialchars($strings);

        return str_replace("'", '', $strings);
    }
}
