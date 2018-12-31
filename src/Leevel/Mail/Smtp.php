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
 * (c) 2010-2019 http://queryphp.com All rights reserved.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Leevel\Mail;

use Swift_SmtpTransport;

/**
 * mail.smtp.
 *
 * @author Xiangmin Liu <635750556@qq.com>
 *
 * @since 2017.08.26
 *
 * @version 1.0
 */
class Smtp extends Connect implements IConnect
{
    /**
     * 配置.
     *
     * @var array
     */
    protected $option = [
        'host'       => 'smtp.qq.com',
        'port'       => 465,
        'username'   => null,
        'password'   => '',
        'encryption' => 'ssl',
    ];

    /**
     * 创建 transport.
     *
     * @return mixed
     */
    public function makeTransport()
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
