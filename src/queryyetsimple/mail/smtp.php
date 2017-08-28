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

use Swift_SmtpTransport;
use queryyetsimple\mail\abstracts\connect;
use queryyetsimple\mail\interfaces\connect as interfaces_connect;

/**
 * mail.smtp
 *
 * @author Xiangmin Liu <635750556@qq.com>
 * @package $$
 * @since 2017.08.26
 * @version 1.0
 */
class smtp extends connect implements interfaces_connect {
    
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
        $objTransport = Swift_SmtpTransport::newInstance ( $this->getOption ( 'host' ), $this->getOption ( 'port' ) );
        
        if (! is_null ( $this->getOption ( 'encryption' ) )) {
            $objTransport->setEncryption ( $this->getOption ( 'encryption' ) );
        }
        
        if (! is_null ( $this->getOption ( 'username' ) )) {
            $objTransport->setUsername ( $this->getOption ( 'username' ) );
            $objTransport->setPassword ( $this->getOption ( 'password' ) );
        }
        
        return $objTransport;
    }
}
