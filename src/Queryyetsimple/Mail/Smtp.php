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
 * (c) 2010-2018 http://queryphp.com All rights reserved.
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
    protected $arrOption = [
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
        $objTransport = Swift_SmtpTransport::newInstance($this->getOption('host'), $this->getOption('port'));

        if (null !== $this->getOption('encryption')) {
            $objTransport->setEncryption($this->getOption('encryption'));
        }

        if (null !== $this->getOption('username')) {
            $objTransport->setUsername($this->getOption('username'));
            $objTransport->setPassword($this->getOption('password'));
        }

        return $objTransport;
    }
}
