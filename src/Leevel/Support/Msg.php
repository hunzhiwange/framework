<?php

declare(strict_types=1);

namespace Leevel\Support;

use Leevel\I18n\Gettext;

/**
 * 消息属性.
 */
#[\Attribute(\Attribute::TARGET_CLASS_CONSTANT)]
readonly class Msg
{
    private string $message;

    public function __construct(string $message = '', null|bool|float|int|string ...$moreArgs)
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
    /**
     * @codeCoverageIgnore
     */
    function __(string $text, null|bool|float|int|string ...$data): string
    {
        if (!class_exists(Gettext::class)) {
            return sprintf($text, ...$data);
        }

        return Gettext::handle($text, ...$data);
    }
}
