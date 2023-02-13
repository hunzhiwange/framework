<?php

declare(strict_types=1);

namespace Leevel\Support;

use Leevel\I18n\Gettext;

/**
 * 消息属性.
 */
#[\Attribute]
class Msg
{
    private readonly string $message;

    public function __construct(string $message = '', ...$moreArgs) // @phpstan-ignore-line
    {
        if ($moreArgs) {
            $message = __($message, ...$moreArgs);
        }

        $this->message = $message;
    }

    public function __invoke(): string
    {
        return $this->message;
    }
}

if (!\function_exists(__NAMESPACE__.'\\__')) {
    function __(string $text, ...$data): string
    {
        if (!class_exists(Gettext::class)) {
            return sprintf($text, ...$data);
        }

        return Gettext::handle($text, ...$data);
    }
}

function xx(): string
{
    return 'xx';
}
