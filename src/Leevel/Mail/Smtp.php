<?php

declare(strict_types=1);

namespace Leevel\Mail;

use Swift_SmtpTransport;

/**
 * smtp 邮件.
 */
class Smtp extends Mail implements IMail
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
        'host'       => 'smtp.qq.com',
        'port'       => 465,
        'username'   => null,
        'password'   => '',
        'encryption' => 'ssl',
    ];

    /**
     * {@inheritDoc}
     */
    protected function makeTransport(): object
    {
        $transport = new Swift_SmtpTransport(
            $this->option['host'],
            $this->option['port']
        );

        if (null !== $this->option['encryption']) {
            $transport->setEncryption(
                $this->option['encryption']
            );
        }

        if (null !== $this->option['username']) {
            $transport->setUsername(
                $this->option['username']
            );
            $transport->setPassword(
                $this->option['password']
            );
        }

        return $transport;
    }
}
