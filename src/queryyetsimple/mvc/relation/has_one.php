<?php
// [$QueryPHP] The PHP Framework For Code Poem As Free As Wind. <Query Yet Simple>
// ©2010-2017 http://queryphp.com All rights reserved.
namespace queryyetsimple\mvc\relation;

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

use queryyetsimple\collection\collection;

/**
 * 关联模型 has_one
 *
 * @author Xiangmin Liu <635750556@qq.com>
 * @package $$
 * @since 2017.09.28
 * @version 1.0
 */
class has_one extends has_many {
    
    /**
     * 查询关联对象
     *
     * @return mixed
     */
    public function sourceQuery() {
        return $this->objSelect->getOne ();
        ;
    }
    
    /**
     * 匹配关联查询数据到模型
     *
     * @param \queryyetsimple\mvc\interfaces\model[] $arrModel            
     * @param \queryyetsimple\collection $objResult            
     * @param string $strRelation            
     * @return array
     */
    public function matchPreLoad(array $arrModel, collection $objResult, $strRelation) {
        return $this->matchPreLoadOneOrMany ( $arrModel, $objResult, $strRelation, 'one' );
    }
}
