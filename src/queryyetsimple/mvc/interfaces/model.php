<?php
// [$QueryPHP] The PHP Framework For Code Poem As Free As Wind. <Query Yet Simple>
// ©2010-2017 http://queryphp.com All rights reserved.
namespace queryyetsimple\mvc\interfaces;

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

/**
 * model 接口
 *
 * @author Xiangmin Liu <635750556@qq.com>
 * @package $$
 * @since 2017.07.10
 * @version 1.0
 */
interface model {
    
    /**
     * 新建时间字段
     *
     * @var string
     */
    const CREATED_AT = 'created_at';
    
    /**
     * 更新时间字段
     *
     * @var string
     */
    const UPDATED_AT = 'updated_at';
}
