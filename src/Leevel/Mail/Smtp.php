<?php

declare(strict_types=1);

/*
 * This file is part of the ************************ package.
 * _____________                           _______________
 *  ______/     \__  _____  ____  ______  / /_  _________
 *   ____/ __   / / / / _ \/ __`\/ / __ \/ __ \/ __ \___
 *    __/ / /  / /_/ /  __/ /  \  / /_/ / / / / /_/ /__
 *      \_\ \_/\____/\___/_/   / / .___/_/ /_/ .___/
 *         \_\                /_/_/         /_/
 *
 * The PHP Framework For Code Poem As Free As Wind. <Query Yet Simple>
 * (c) 2010-2020 http://queryphp.com All rights reserved.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

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
     * {@inheritdoc}
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
