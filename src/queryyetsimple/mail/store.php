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

use Swift_Image;
use Swift_Message;
use Swift_Attachment;
use BadMethodCallException;
use InvalidArgumentException;
use queryyetsimple\mvc\iview;
use queryyetsimple\support\assert;
use queryyetsimple\support\option;
use queryyetsimple\support\flow_control;
use queryyetsimple\mail\interfaces\connect;
use queryyetsimple\mail\interfaces\store as interfaces_store;

/**
 * mail 存储
 *
 * @author Xiangmin Liu <635750556@qq.com>
 * @package $$
 * @since 2017.08.26
 * @version 1.0
 */
class store implements interfaces_store {
    
    use option;
    use flow_control;
    
    /**
     * 连接驱动
     *
     * @var \queryyetsimple\mail\interfaces\connect
     */
    protected $oConnect;
    
    /**
     * 视图
     *
     * @var \queryyetsimple\mvc\iview
     */
    protected $objView;
    
    /**
     * 事件
     *
     * @var \queryyetsimple\event\interfaces\dispatch|null
     */
    protected $objEvent;
    
    /**
     * 邮件错误消息
     *
     * @var array
     */
    protected $arrFailedRecipients = [ ];
    
    /**
     * 消息
     *
     * @var \queryyetsimple\mail\message
     */
    protected $objMessage;
    
    /**
     * 消息配置
     *
     * @var array
     */
    protected $arrMessageData = [ 
            'html' => [ ],
            'plain' => [ ] 
    ];
    
    /**
     * 配置
     *
     * @var array
     */
    protected $arrOption = [ 
            'global_from' => [ 
                    'address' => null,
                    'name' => null 
            ],
            'global_to' => [ 
                    'address' => null,
                    'name' => null 
            ] 
    ];
    
    /**
     * 构造函数
     *
     * @param array $arrOption            
     * @param \queryyetsimple\mail\interfaces\connect $oConnect            
     * @param \queryyetsimple\mvc\iview $objView            
     * @param \queryyetsimple\event\interfaces\dispatch|null $objEvent            
     * @return void
     */
    public function __construct(array $arrOption = [], connect $oConnect, iview $objView, $objEvent = null) {
        $this->options ( $arrOption );
        $this->oConnect = $oConnect;
        $this->objView = $objView;
        $this->objEvent = $objEvent;
    }
    
    /**
     * 设置邮件发送来源
     *
     * @param string $strAddress            
     * @param string|null $mixName            
     * @return $this
     */
    public function globalFrom($strAddress, $mixName = null) {
        $this->option ( 'global_from', compact ( 'strAddress', 'mixName' ) );
        return $this;
    }
    
    /**
     * 设置邮件发送地址
     *
     * @param string $strAddress            
     * @param string|null $mixName            
     * @return $this
     */
    public function globalTo($strAddress, $mixName = null) {
        $this->option ( 'global_to', compact ( 'strAddress', 'mixName' ) );
        return $this;
    }
    
    /**
     * 视图 html 邮件内容
     *
     * @param string $sFile            
     * @param array $arrData            
     * @return $this
     */
    public function view($sFile, array $arrData = []) {
        if ($this->checkFlowControl ())
            return $this;
        
        $this->arrMessageData ['html'] [] = [ 
                'file' => $sFile,
                'data' => $arrData 
        ];
        return $this;
    }
    
    /**
     * html 邮件内容
     *
     * @param string $strContent            
     * @return $this
     */
    public function html($strContent) {
        if ($this->checkFlowControl ())
            return $this;
        
        assert::scalar ( $strContent );
        
        $this->arrMessageData ['html'] [] = $strContent;
        return $this;
    }
    
    /**
     * 纯文本邮件内容
     *
     * @param string $strContent            
     * @return $this
     */
    public function plain($strContent) {
        if ($this->checkFlowControl ())
            return $this;
        
        assert::scalar ( $strContent );
        
        $this->arrMessageData ['plain'] [] = $strContent;
        return $this;
    }
    
    /**
     * 视图纯文本邮件内容
     *
     * @param string $sFile            
     * @param array $arrData            
     * @return $this
     */
    public function viewPlain($sFile, array $arrData = []) {
        if ($this->checkFlowControl ())
            return $this;
        
        $this->arrMessageData ['plain'] [] = [ 
                'file' => $sFile,
                'data' => $arrData 
        ];
        return $this;
    }
    
    /**
     * 消息回调处理
     *
     * @param callable|string $mixCallback            
     * @return $this
     */
    public function message($mixCallback) {
        $this->callbackMessage ( $mixCallback, $this->makeMessage () );
        return $this;
    }
    
    /**
     * 添加附件
     *
     * @param string $strFile            
     * @param callable|null $mixCallback            
     * @return $this
     */
    public function attach($strFile, $mixCallback = null) {
        $this->makeMessage ();
        return $this->callbackAttachment ( $this->createPathAttachment ( $strFile ), $mixCallback );
    }
    
    /**
     * 添加内存内容附件
     * file_get_content( path )
     *
     * @param string $strData            
     * @param string $strName            
     * @param callable|null $mixCallback            
     * @return $this
     */
    public function attachData($strData, $strName, $mixCallback = null) {
        $this->makeMessage ();
        return $this->callbackAttachment ( $this->createDataAttachment ( $strData, $strName ), $mixCallback );
    }
    
    /**
     * 图片嵌入邮件
     *
     * @param string $file            
     * @return string
     */
    public function attachView($strFile) {
        $this->makeMessage ();
        return $this->objMessage->embed ( Swift_Image::fromPath ( $strFile ) );
    }
    
    /**
     * 内存内容图片嵌入邮件
     *
     * @param string $strData            
     * @param string $strName            
     * @param string|null $contentType            
     * @return string
     */
    public function attachDataView($strData, $strName, $strContentType = null) {
        $this->makeMessage ();
        return $this->objMessage->embed ( Swift_Image::newInstance ( $strData, $strName, $strContentType ) );
    }
    
    /**
     * 格式化中文附件名字
     *
     * @param string $strFile            
     * @return string
     */
    public function attachChinese($strFile) {
        $strExt = pathinfo ( $strFile, PATHINFO_EXTENSION );
        if ($strExt) {
            $strFile = substr ( $strFile, 0, strrpos ( $strFile, '.' . $strExt ) );
        }
        return '=?UTF-8?B?' . base64_encode ( $strFile ) . '?=' . ($strExt ? '.' . $strExt : '');
    }
    
    /**
     * 发送邮件
     *
     * @param callable|string $mixCallback            
     * @param boolean $booHtmlPriority            
     * @return int
     */
    public function send($mixCallback = null, $booHtmlPriority = true) {
        $this->makeMessage ();
        
        $this->parseMailContent ( $booHtmlPriority );
        
        if ($mixCallback) {
            $this->message ( $mixCallback );
        }
        
        if (! empty ( $this->getOption ( 'global_to' )['address'] )) {
            $this->objMessage->addTo ( $this->getOption ( 'global_to' )['address'], $this->getOption ( 'global_to' )['name'] );
        }
        
        return $this->sendMessage ( $this->objMessage );
    }
    
    /**
     * 错误消息
     *
     * @return array
     */
    public function failedRecipients() {
        return $this->arrFailedRecipients;
    }
    
    /**
     * 试图渲染数据
     *
     * @param string $strFile            
     * @param array $arrData            
     * @return string
     */
    protected function getViewData($strFile, array $arrData) {
        return $this->objView->clearAssign ()->assign ( 'objMail', $this )->assign ( $arrData )->display ( $strFile, [ 
                'return' => true 
        ] );
    }
    
    /**
     * 解析邮件内容
     *
     * @param boolean $booHtmlPriority            
     * @return void
     */
    protected function parseMailContent($booHtmlPriority = true) {
        $booFind = false;
        
        $arrMessageData = $this->arrMessageData;
        
        if (! empty ( $arrMessageData ['html'] ) && ! empty ( $arrMessageData ['plain'] )) {
            unset ( $arrMessageData [$booHtmlPriority === true ? 'plain' : 'html'] );
        }
        
        if (! empty ( $arrMessageData ['html'] )) {
            foreach ( $arrMessageData ['html'] as $mixView ) {
                if ($booFind === false) {
                    $strMethod = 'setBody';
                    $booFind = true;
                } else {
                    $strMethod = 'addPart';
                }
                
                $this->objMessage->$strMethod ( is_array ( $mixView ) ? $this->getViewData ( $mixView ['file'], $mixView ['data'] ) : $mixView, 'text/html' );
            }
        }
        
        if (! empty ( $arrMessageData ['plain'] )) {
            foreach ( $arrMessageData ['plain'] as $mixView ) {
                if ($booFind === false) {
                    $strMethod = 'setBody';
                    $booFind = true;
                } else {
                    $strMethod = 'addPart';
                }
                
                $this->objMessage->$strMethod ( is_array ( $mixView ) ? $this->getViewData ( $mixView ['file'], $mixView ['data'] ) : $mixView, 'text/plain' );
            }
        }
    }
    
    /**
     * 发送消息对象
     *
     * @param \Swift_Message $objMessage            
     * @return int
     */
    protected function sendMessage(Swift_Message $objMessage) {
        return $this->oConnect->send ( $objMessage, $this->arrFailedRecipients );
    }
    
    /**
     * 创建消息对象
     *
     * @return \Swift_Message
     */
    protected function makeMessage() {
        if (! is_null ( $this->objMessage )) {
            return $this->objMessage;
        }
        
        $oMessage = new Swift_Message ();
        
        if (! empty ( $this->getOption ( 'global_from' )['address'] )) {
            $oMessage->setFrom ( $this->getOption ( 'global_from' )['address'], $this->getOption ( 'global_from' )['name'] );
        }
        
        return $this->objMessage = $oMessage;
    }
    
    /**
     * 邮件消息回调处理
     *
     * @param callable|string $mixCallback            
     * @param \Swift_Message $objMessage            
     * @return mixed
     */
    protected function callbackMessage($mixCallback, Swift_Message $objMessage) {
        if (is_callable ( $mixCallback )) {
            return call_user_func_array ( $mixCallback, [ 
                    $objMessage,
                    $this 
            ] );
        }
        
        if (is_string ( $mixCallback )) {
            if (strpos ( $mixCallback, '@' ) !== false) {
                $arrCallback = explode ( '@', $mixCallback );
                if (empty ( $arrCallback [1] ))
                    $arrCallback [1] = 'handle';
            } else {
                $arrCallback = [ 
                        $mixCallback,
                        'handle' 
                ];
            }
            
            if (($mixCallback = $this->objContainer->make ( $arrCallback [0] )) === false)
                throw new InvalidArgumentException ( sprintf ( 'Message callback %s is not valid', $arrCallback [0] ) );
            
            $strMethod = method_exists ( $mixCallback, $arrCallback [1] ) ? $arrCallback [1] : ($arrCallback [1] != 'handle' && method_exists ( $mixCallback, 'handle' ) ? 'handle' : 'run');
            
            return call_user_func_array ( [ 
                    $mixCallback,
                    $strMethod 
            ], [ 
                    $objMessage,
                    $this 
            ] );
        }
        
        throw new InvalidArgumentException ( 'Message callback is not valid' );
    }
    
    /**
     * 路径创建 Swift_Attachment
     *
     * @param string $strFile            
     * @return \Swift_Attachment
     */
    protected function createPathAttachment($strFile) {
        return Swift_Attachment::fromPath ( $strFile );
    }
    
    /**
     * 内存内容创建 Swift_Attachment
     *
     * @param string $strData            
     * @param string $strName            
     * @return \Swift_Attachment
     */
    protected function createDataAttachment($strData, $strName) {
        return Swift_Attachment::newInstance ( $strData, $strName );
    }
    
    /**
     * 邮件附件消息回调处理
     *
     * @param \Swift_Attachment $objAttachment            
     * @param callable|null $mixCallback            
     * @return $this
     */
    protected function callbackAttachment($objAttachment, $mixCallback = null) {
        if (is_callable ( $mixCallback )) {
            call_user_func_array ( $mixCallback, [ 
                    $objAttachment,
                    $this 
            ] );
            $this->objMessage->attach ( $objAttachment );
        }
        
        return $this;
    }
    
    /**
     * 缺省方法
     *
     * @param 方法名 $sMethod            
     * @param 参数 $arrArgs            
     * @return mixed
     */
    public function __call($sMethod, $arrArgs) {
        if ($this->placeholderFlowControl ( $sMethod )) {
            return $this;
        }
        
        return call_user_func_array ( [ 
                $this->oConnect,
                $sMethod 
        ], $arrArgs );
    }
}
