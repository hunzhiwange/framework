<?php

declare(strict_types=1);

namespace Leevel\Database;

/**
 * 连接异常.
 */
class ConnectionException extends \RuntimeException
{
    public static function noActiveTransaction(string $extendMessage = ''): static
    {
        $extendMessage = $extendMessage ? '['.$extendMessage.']' : '';

        throw new static($extendMessage.'There was no active transaction.');
    }

    public static function rollbackOnly(): static
    {
        throw new static('Only transaction rollback is allowed.');
    }

    public static function errModeExceptionOnly(): static
    {
        $message = 'PDO query property \PDO::ATTR_ERRMODE cannot be set,it is always \PDO::ERRMODE_EXCEPTION.';

        throw new static($message);
    }
}
