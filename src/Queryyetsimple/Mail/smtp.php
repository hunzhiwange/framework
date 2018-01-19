<?php
/*
 * This file is part of the ************************ package.
 * ##########################################################
 * #   ____                          ______  _   _ ______   #
 * #  /     \       ___  _ __  _   _ | ___ \| | | || ___ \  #
 * # |   (  ||(_)| / _ \| '__|| | | || |_/ /| |_| || |_/ /  #
 * #  \____/ |___||  __/| |   | |_| ||  __/ |  _  ||  __/   #
 * #       \__   | \___ |_|    \__  || |    | | | || |      #
 * #     Query Yet Simple      __/  |\_|    |_| |_|\_|      #
 * #                          |___ /  Since 2010.10.03      #
 * ##########################################################
 *
 * The PHP Framework For Code Poem As Free As Wind. <Query Yet Simple>
 * (c) 2010-2018 http://queryphp.com All rights reserved.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace queryyetsimple\mail;

use Swift_SmtpTransport;

/**
 * mail.smtp
 *
 * @author Xiangmin Liu <635750556@qq.com>
 * @package $$
 * @since 2017.08.26
 * @version 1.0
 */
class smtp extends aconnect implements iconnect
{

    /**
     * 配置
     *
     * @var array
     */
    protected $arrOption = [
        'host' => 'smtp.qq.com',
        'port' => 465,
        'username' => null,
        'password' => '',
        'encryption' => 'ssl'
    ];

    /**
     * 创建 transport
     *
     * @return mixed
     */
    public function makeTransport()
    {
        $objTransport = Swift_SmtpTransport::newInstance($this->getOption('host'), $this->getOption('port'));

        if (! is_null($this->getOption('encryption'))) {
            $objTransport->setEncryption($this->getOption('encryption'));
        }

        if (! is_null($this->getOption('username'))) {
            $objTransport->setUsername($this->getOption('username'));
            $objTransport->setPassword($this->getOption('password'));
        }

        return $objTransport;
    }
}
