<?php
// [$QueryPHP] The PHP Framework For Code Poem As Free As Wind. <Query Yet Simple>
// ©2010-2017 http://queryphp.com All rights reserved.
namespace queryyetsimple\mail;

<<<queryphp
##########################################################
#   ____                          ______  _   _ ______   #
#  /     \       ___  _ __  _   _ | ___ \| | | || ___ \  #
# |   (  ||(_)| / _ \| '__|| | | || |_/ /| |_| || |_/ /  #
#  \____/ |___||  __/| |   | |_| ||  __/ |  _  ||  __/   #
#       \__   | \___ |_|    \__  || |    | | | || |      #
#     Query Yet Simple      __/  |\_|    |_| |_|\_|      #
#                          |___ /  Since 2010.10.03      #
##########################################################
queryphp;

use Swift_SendmailTransport;
use queryyetsimple\mail\abstracts\connect;
use queryyetsimple\mail\interfaces\connect as interfaces_connect;

/**
 * mail.sendmail
 *
 * @author Xiangmin Liu <635750556@qq.com>
 * @package $$
 * @since 2017.08.28
 * @version 1.0
 */
class sendmail extends connect implements interfaces_connect {
    
    /**
     * 配置
     *
     * @var array
     */
    protected $arrOption = [ 
            'path' => '/usr/sbin/sendmail -bs' 
    ];
    
    /**
     * 构造函数
     *
     * @param array $arrOption            
     * @return void
     */
    public function __construct(array $arrOption = []) {
        parent::__construct ( $arrOption );
        
        $this->swiftMailer ();
    }
    
    /**
     * 创建 transport
     *
     * @return mixed
     */
    public function makeTransport() {
        return Swift_SendmailTransport::newInstance ( $this->getOption ( 'path' ) );
    }
}
