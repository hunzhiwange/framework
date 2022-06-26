<?php

declare(strict_types=1);

namespace Leevel\Encryption\Helper;

/**
 * 深度过滤.
 */
class DeepReplace
{
    public static function handle(array $search, string $subject): string
    {
        $found = true;
        while ($found) {
            $found = false;
            foreach ($search as $val) {
                while (false !== strpos($subject, $val)) {
                    $found = true;
                    $subject = str_replace($val, '', $subject);
                }
            }
        }

        return $subject;
    }
}
