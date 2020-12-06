<?php

declare(strict_types=1);

namespace Leevel\Encryption\Helper;

/**
 * 深度过滤.
 */
function deep_replace(array $search, string $subject): string
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

class deep_replace
{
}
