<?php
// [$QueryPHP] The PHP Framework For Code Poem As Free As Wind. <Query Yet Simple>
// ©2010-2017 http://queryphp.com All rights reserved.
namespace queryyetsimple\filesystem\abstracts;

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

use League\Flysystem\Filesystem;
use queryyetsimple\classs\option;

/**
 * connect 驱动抽象类
 *
 * @author Xiangmin Liu <635750556@qq.com>
 * @package $$
 * @since 2017.08.29
 * @see https://flysystem.thephpleague.com/api/
 * @version 1.0
 */
abstract class connect {
    
    use option;
    
    /**
     * Filesystem
     *
     * @var \League\Flysystem\Filesystem
     */
    protected $objFilesystem;
    
    /**
     * 构造函数
     *
     * @param array $arrOption            
     * @return void
     */
    public function __construct(array $arrOption = []) {
        $this->options ( $arrOption );
        $this->filesystem ();
    }

    /**
     * 返回 Filesystem
     *
     * @return \League\Flysystem\Filesystem
     */
    public function getFilesystem() {
        return $this->objFilesystem;
    }
    
    /**
     * 生成 Filesystem
     *
     * @return \League\Flysystem\Filesystem
     */
    protected function filesystem() {
        return $this->objFilesystem = new Filesystem($this->makeConnect (), $this->getOptions());
    }

    /**
     * 缺省方法
     *
     * @param 方法名 $sMethod            
     * @param 参数 $arrArgs            
     * @return mixed
     */
    public function __call($sMethod, $arrArgs) {
        return call_user_func_array ( [ 
                $this->objFilesystem,
                $sMethod 
        ], $arrArgs );
    }
}
