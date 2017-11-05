<?php
// [$QueryPHP] The PHP Framework For Code Poem As Free As Wind. <Query Yet Simple>
// ©2010-2017 http://queryphp.com All rights reserved.
namespace queryyetsimple\event;

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

use queryyetsimple\support\icontainer;

/**
 * 事件
 *
 * @author Xiangmin Liu <635750556@qq.com>
 * @package $$
 * @since 2017.04.17
 * @version 1.0
 */
class dispatch implements idispatch {
    
    /**
     * 项目容器
     *
     * @var \queryyetsimple\support\icontainer
     */
    protected $objContainer;
    
    /**
     * 注册的监听器
     *
     * @var array
     */
    protected $arrListener = [ ];
    
    /**
     * 创建一个事件解析器
     *
     * @param \queryyetsimple\support\icontainer $objContainer            
     * @return void
     */
    public function __construct(icontainer $objContainer) {
        $this->objContainer = $objContainer;
    }
    
    /**
     * 执行一个事件
     *
     * @param string|object $mixEvent            
     * @return void
     */
    public function run($mixEvent) {
        if (is_object ( $mixEvent )) {
            $objEvent = $mixEvent;
            $mixEvent = get_class ( $mixEvent );
        } else {
            $objEvent = $this->objContainer->make ( $mixEvent );
        }
        
        $arrArgs = func_get_args ();
        array_shift ( $arrArgs );
        if (is_object ( $objEvent ))
            array_unshift ( $arrArgs, $objEvent );
        
        if (! $this->hasListener ( $mixEvent )) {
            return;
        }
        
        call_user_func_array ( [ 
                $this->getListener ( $mixEvent ),
                'notify' 
        ], $arrArgs );
    }
    
    /**
     * 注册监听器
     *
     * @param string|array $mixEvent            
     * @param mixed $mixListener            
     * @return void
     */
    public function listener($mixEvent, $mixListener) {
        foreach ( ( array ) $mixEvent as $strEvent ) {
            $this->registerEventSubject ( $strEvent );
            $this->arrListener [$strEvent]->attachs ( $mixListener );
        }
    }
    
    /**
     * 获取一个监听器
     *
     * @param string $strEvent            
     * @return array
     */
    public function getListener($strEvent) {
        if (isset ( $this->arrListener [$strEvent] )) {
            return $this->arrListener [$strEvent];
        }
        return null;
    }
    
    /**
     * 判断监听器是否存在
     *
     * @param string $strEvent            
     * @return bool
     */
    public function hasListener($strEvent) {
        return isset ( $this->arrListener [$strEvent] );
    }
    
    /**
     * 删除一个事件所有监听器
     *
     * @param string $strEvent            
     * @return void
     */
    public function deleteListener($strEvent) {
        if (isset ( $this->arrListener [$strEvent] )) {
            unset ( $this->arrListener [$strEvent] );
        }
    }
    
    /**
     * 注册事件观察者角色主体
     *
     * @param string $strEvent            
     * @return void
     */
    protected function registerEventSubject($strEvent) {
        if (! isset ( $this->arrListener [$strEvent] ))
            $this->arrListener [$strEvent] = new subject ( $this->objContainer );
    }
}
