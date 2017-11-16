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
 * (c) 2010-2017 http://queryphp.com All rights reserved.
 * 
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace queryyetsimple\mail;

use Swift_SendmailTransport;

/**
 * mail.sendmail
 *
 * @author Xiangmin Liu <635750556@qq.com>
 * @package $$
 * @since 2017.08.28
 * @version 1.0
 */
class sendmail extends aconnect implements iconnect
{
    
    /**
     * 配置
     *
     * @var array
     */
    protected $arrOption = [
        'path' => '/usr/sbin/sendmail -bs'
    ];
    
    /**
     * 创建 transport
     *
     * @return mixed
     */
    public function makeTransport()
    {
        return Swift_SendmailTransport::newInstance($this->getOption('path'));
    }
}
