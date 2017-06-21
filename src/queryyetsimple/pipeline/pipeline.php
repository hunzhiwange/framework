<?php
// [$QueryPHP] A PHP Framework For Simple As Free As Wind. <Query Yet Simple>
// ©2010-2017 http://queryphp.com All rights reserved.
namespace queryyetsimple\pipeline;

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

use InvalidArgumentException;
use queryyetsimple\support\interfaces\container;
use queryyetsimple\pipeline\interfaces\pipeline as interfaces_pipeline;

/**
 * 管道实现类
 *
 * @author Xiangmin Liu<635750556@qq.com>
 * @package $$
 * @since 2017.05.25
 * @version 1.0
 */
class pipeline implements interfaces_pipeline {
    
    /**
     * 容器
     *
     * @var \queryyetsimple\support\interfaces\container
     */
    protected $objContainer;
    
    /**
     * 管道传递的对象
     *
     * @var mixed
     */
    protected $mixPassed;
    
    /**
     * 管道中所有执行工序
     *
     * @var array
     */
    protected $arrStage = [ ];
    
    /**
     * 创建一个管道
     *
     * @param \queryyetsimple\support\interfaces\container $objContainer            
     * @return void
     */
    public function __construct(container $objContainer) {
        $this->objContainer = $objContainer;
    }
    
    /**
     * 将传输对象传入管道
     *
     * @param mixed $mixPassed            
     * @return $this
     */
    public function send($mixPassed) {
        $this->mixPassed = $mixPassed;
        return $this;
    }
    
    /**
     * 设置管道中的执行工序
     *
     * @param dynamic|array $mixStages            
     * @return $this
     */
    public function through($mixStages /* args */ ){
        $mixStages = is_array ( $mixStages ) ? $mixStages : func_get_args ();
        
        foreach ( $mixStages as $mixStage ) {
            if (is_string ( $mixStage )) {
                $mixStage = $this->parse ( $mixStage );
                $mixStage = function ($mixPassed) use($mixStage) {
                    if (strpos ( $mixStage [0], '@' ) !== false) {
                        $arrStage = explode ( '@', $mixStage [0] );
                        if (empty ( $arrStage [1] ))
                            $arrStage [1] = 'handle';
                    } else {
                        $arrStage = [ 
                                $mixStage [0],
                                'handle' 
                        ];
                    }
                    
                    if (($objStage = $this->objContainer->make ( $arrStage [0] )) === false)
                        throw new InvalidArgumentException ( sprintf ( 'Stage %s is not valid.', $arrStage [0] ) );
                    
                    $strMethod = method_exists ( $objStage, $arrStage [1] ) ? $arrStage [1] : ($arrStage [1] != 'handle' && method_exists ( $objStage, 'handle' ) ? 'handle' : 'run');
                    $mixStage = $mixStage [1];
                    array_unshift ( $mixStage, $mixPassed );
                    array_unshift ( $mixStage, [ 
                            $objStage,
                            $strMethod 
                    ] );
                    
                    return call_user_func_array ( [ 
                            $this->objContainer,
                            'call' 
                    ], $mixStage );
                };
            }
            $this->stage ( $mixStage );
        }
        return $this;
    }
    
    /**
     * 添加一道工序
     *
     * @param callable $calStage            
     * @return $this
     */
    public function stage(callable $calStage) {
        if (! is_callable ( $calStage )) {
            throw new InvalidArgumentException ( sprintf ( 'Stage %s is not valid.', '$calStage' ) );
        }
        $this->arrStage [] = $calStage;
        return $this;
    }
    
    /**
     * 执行管道工序响应结果
     *
     * @param callable $calEnd            
     * @return mixed
     */
    public function then(callable $calEnd) {
        return call_user_func_array ( $calEnd, [ 
                $this->run () 
        ] );
    }
    
    /**
     * 执行管道工序
     *
     * @return mixed
     */
    public function run() {
        foreach ( $this->arrStage as $calStage ) {
            $this->mixPassed = call_user_func ( $calStage, $this->mixPassed );
        }
        return $this->mixPassed;
    }
    
    /**
     * 解析工序
     *
     * @param string $strStage            
     * @return array
     */
    protected function parse($strStage) {
        list ( $strName, $arrArgs ) = array_pad ( explode ( ':', $strStage, 2 ), 2, [ ] );
        if (is_string ( $arrArgs ))
            $arrArgs = explode ( ',', $arrArgs );
        return [ 
                $strName,
                $arrArgs 
        ];
    }
}
