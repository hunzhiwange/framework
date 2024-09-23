<?php

declare(strict_types=1);

namespace Leevel\Validate;

use Leevel\I18n\Gettext;

trait PrepareMessage
{
    /**
     * 默认验证提示信息.
     */
    protected static array $defaultMessages = [];

    protected static function parepareMessage(string $classHelper, string $rule, bool $antisenseRule): void
    {
        // 验证器默认消息
        if ($antisenseRule) {
            if (!isset(static::$defaultMessages['!'.$rule])) {
                $message = \defined($classHelper.'::ANTISENSE_MESSAGE') ? __($classHelper::ANTISENSE_MESSAGE) : '';
                if (!$message && \defined($classHelper.'::MESSAGE')) {
                    $message = static::parepareAntisenseMessage(__($classHelper::MESSAGE));
                }
                if ($message) {
                    static::$defaultMessages['!'.$rule] = $message;
                }
            }
        } elseif (!isset(static::$defaultMessages[$rule])) {
            static::$defaultMessages[$rule] = \defined($classHelper.'::MESSAGE') ? __($classHelper::MESSAGE) : '';
        }
    }

    protected static function parepareAntisenseMessage(string $message): string
    {
        return __('不满足【%s】', $message);
    }
}

if (!\function_exists(__NAMESPACE__.'\\__')) {
    function __(string $text, null|bool|float|int|string ...$data): string
    {
        if (!class_exists(Gettext::class)) {
            return sprintf($text, ...$data); // @codeCoverageIgnore
        }

        return Gettext::handle($text, ...$data);
    }
}
