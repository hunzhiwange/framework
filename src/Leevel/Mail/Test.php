<?php

declare(strict_types=1);

namespace Leevel\Mail;

use Swift_NullTransport;

/**
 * test 邮件.
 */
class Test extends Mail implements IMail
{
    /**
     * 配置.
     */
    protected array $option = [];

    /**
     * {@inheritDoc}
     */
    protected function makeTransport(): object
    {
        return new Swift_NullTransport();
    }
}
