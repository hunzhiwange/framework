<?php

declare(strict_types=1);

namespace Leevel\Mail;

use Swift_SendmailTransport;

/**
 * sendmail 邮件.
 */
class Sendmail extends Mail implements IMail
{
    /**
     * 配置.
     */
    protected array $option = [
        'global_from' => [
            'address' => null,
            'name'    => null,
        ],
        'global_to' => [
            'address' => null,
            'name'    => null,
        ],
        'path' => '/usr/sbin/sendmail -bs',
    ];

    /**
     * {@inheritDoc}
     */
    protected function makeTransport(): object
    {
        return new Swift_SendmailTransport($this->option['path']);
    }
}
